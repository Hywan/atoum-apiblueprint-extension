<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use Hoa\Ustring;
use atoum\apiblueprint\IntermediateRepresentation as IR;
use mageekguy\atoum\writers\file;

class Target
{
    public function compile(IR\Document $document, file $outputFile)
    {
        $outputFile->write('namespace atoum\apiblueprint\generated;' . "\n\n");

        $testSuiteName = $document->apiName;

        if (empty($testSuiteName)) {
            $testSuiteName = 'Unknown' . sha1(serialize($document));
        } else {
            $testSuiteName = $this->stringToPHPIdentifier($testSuiteName, false);
        }

        $outputFile->write(
            'class ' . $testSuiteName . ' extends \atoum\apiblueprint\test' . "\n" .
            '{' . "\n" .
            '    protected $_host = null;' . "\n\n" .
            '    public function setUp()' . "\n" .
            '    {' . "\n" .
            '        $this->_host = \'' . $document->metadata['host'] . '\';' . "\n" .
            '    }'
        );

        foreach ($document->resources as $i => $resource) {
            $resourceName = $resource->name;

            if (empty($resourceName)) {
                $resourceName = 'test resource unknonwn' . $i;
            } else {
                $resourceName = 'test resource ' . $this->stringToPHPIdentifier($resourceName);
            }

            $this->compileResource($resource, $resourceName, $outputFile);
        }

        $outputFile->write("\n" . '}');
    }

    public function compileResource(IR\Resource $resource, string $resourceName, file $outputFile)
    {
        if (empty($resource->actions)) {
            $outputFile->write(
                "\n\n" .
                '    public function ' . $resourceName . '()' . "\n" .
                '    {' . "\n" .
                '        $this->skip(\'No action for the resource `' . $resource->name . '`.\');' . "\n" .
                '    }'
            );

            return;
        }

        foreach ($resource->actions as $i => $action) {
            $actionName = $action->name;

            if (empty($actionName)) {
                $actionName = 'action unknown' . $i;
            } else {
                $actionName = 'action ' . $this->stringToPHPIdentifier($actionName);
            }

            $this->compileAction($action, $actionName, $resource, $resourceName, $outputFile);
        }
    }

    public function compileAction(IR\Action $action, string $actionName, IR\Resource $resource, string $resourceName, file $outputFile)
    {
        if (empty($action->messages)) {
            $outputFile->write(
                "\n\n" .
                '    public function ' . $resourceName . ' ' . $actionName . '()' . "\n" .
                '    {' . "\n" .
                '        $this->skip(\'Action `' .$action->name . '` for the resource `' . $resource->name . '` has no message.\');' . "\n" .
                '    }'
            );
        }
    }

    public function stringToPHPIdentifier(string $string, bool $toLowerCase = true): string
    {
        $identifier = (new UString($string))->toAscii()->replace('/[^a-zA-Z0-9_\x80-\xff]/', ' ');

        if (true === $toLowerCase) {
            $identifier->toLowerCase();
        }

        return trim((string) $identifier, ' ');
    }
}
