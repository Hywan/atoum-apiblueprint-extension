<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum\writers\file;

class compiler
{
    protected static $_parser = null;
    protected static $_target = null;

    public function compile(iterable $finder, file $outputFile = null, parser $parser = null, target $target = null)
    {
        if (null === $outputFile) {
            $outputDirectory = sys_get_temp_dir() . '/atoum/apiblueprint/';

            if (false === is_dir($outputDirectory)) {
                mkdir($outputDirectory, 0777, true);
            }

            $outputFileName = $outputDirectory . '/' . sha1(__DIR__ . uniqid()) . '.php';

            if (true === file_exists($outputFileName)) {
                unlink($outputFileName);
            }

            $outputFile = new file($outputFileName);
        }

        $parser = $parser ?? static::getParser();
        $target = $target ?? static::getTarget();

        foreach ($finder as $splFileInfo) {
            $intermediateRepresentation = $parser->parse(
                file_get_contents($splFileInfo->getPathname())
            );
            $target->compile($intermediateRepresentation, $outputFile);
        }
    }

    public static function getParser(): parser
    {
        if (null === static::$_parser) {
            static::$_parser = new parser();
        }

        return static::$_parser;
    }

    public static function getTarget(): target
    {
        if (null === static::$_target) {
            static::$_target = new target();
        }

        return static::$_target;
    }
}
