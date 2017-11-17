<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use atoum\apiblueprint\compiler as SUT;
use atoum\apiblueprint as LUT;
use mageekguy\atoum\test;

class compiler extends test
{
    public function test_compile_with_no_output_file()
    {
        $self = $this;

        $this
            ->given(
                $compiler = new SUT(),
                $this->function->file_exists = function ($path) use ($self): bool {
                    $self
                        ->string($path)
                            ->matches('#^' . sys_get_temp_dir() . '/atoum/apiblueprint/([^\.]+)\.php$#');

                    return false;
                }
            )
            ->when($result = $compiler->compile(new LUT\finder()))
            ->then
                ->function('file_exists')->once();
    }

    public function test_get_parser()
    {
        $this
            ->when($result = SUT::getParser())
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\parser::class)
                    ->isIdenticalTo(SUT::getParser());
    }

    public function test_get_target()
    {
        $this
            ->when($result = SUT::getTarget())
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\target::class)
                    ->isIdenticalTo(SUT::getTarget());
    }
}
