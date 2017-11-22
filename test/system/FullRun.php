<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\system;

use atoum\apiblueprint\Parser;
use atoum\apiblueprint\Target;
use mageekguy\atoum\test;
use mageekguy\atoum\writers\file;

class FullRun extends test
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_from_apib_files_to_atoum_tests()
    {
        $files     = new \FilesystemIterator(dirname(__DIR__) . '/fixtures/system/fullRun');
        $parser    = new Parser();
        $target    = new Target();
        $uri       = stream_get_meta_data(tmpfile())['uri'];
        $collector = new file($uri);

        $this
            ->executeOnFailure(
                function () use (&$file) {
                    echo 'Using file `', $file->getBasename(), '`.', "\n";
                }
            );

        foreach ($files as $file) {
            [$input, $output] = preg_split('/\s+---\[to\]---\s+/', file_get_contents($file->getPathname()));

            $target->compile($parser->parse($input), $collector);

            $this
                ->string($output)
                    ->isEqualTo(file_get_contents($uri));

            $collector->clear();
        }
    }
}
