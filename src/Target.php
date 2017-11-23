<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum\writers\file;

class Target
{
    public function compile(IntermediateRepresentation\Document $document, file $outputFile)
    {
        $outputFile->write('namespace atoum\apiblueprint\generated;' . "\n\n");

        $testSuiteName = $document->apiName;

        if (empty($testSuiteName)) {
            $testSuiteName = 'Unknown' . sha1(serialize($document));
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

        $outputFile->write("\n" . '}');
    }
}
