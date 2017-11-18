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
            ->given($parser = new SUT())
            ->when($result = $parser->parse(''))
            ->then
                ->object($result)
                    ->isEqualTo(new IR\Document());
    }
}
