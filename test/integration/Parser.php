<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use League\CommonMark\Block\Element as Block;
use League\CommonMark\Inline\Element as Inline;
use atoum\apiblueprint as LUT;
use atoum\apiblueprint\IntermediateRepresentation as IR;
use atoum\apiblueprint\Parser as SUT;
use mageekguy\atoum\test;

class Parser extends test
{
    public function test_empty_string()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = ''
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->object($result)
                    ->isEqualTo(new IR\Document());
    }

    public function test_format_and_host()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = "\n\n\n" . 'FORMAT: 1A' . "\n" . 'HOST: https://example.org/'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->object($result)
                    ->isInstanceOf(IR\Document::class)
                ->array($result->metadata)
                    ->isEqualTo([
                        'format' => '1A',
                        'host'   => 'https://example.org/'
                    ]);
    }
}
