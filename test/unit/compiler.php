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

    public function test_compile_to_an_empty_target()
    {
        $self = $this;

        $this
            ->given(
                $finder = new LUT\finder(),
                $finder->getInnerIterator()->append(new \FilesystemIterator(dirname(__DIR__) . '/fixtures/finder/z')),

                $this->mockGenerator->orphanize('__construct'),
                $outputFile = new \mock\mageekguy\atoum\writers\file(),

                $parser                        = new \mock\atoum\apiblueprint\parser(),
                $document                      = new LUT\intermediateRepresentation\document(),
                $this->calling($parser)->parse = $document,

                $target = new \mock\atoum\apiblueprint\target(),
                $this->calling($target)->compile->doesNothing(),

                $compiler = new SUT()
            )
            ->when($result = $compiler->compile($finder, $outputFile, $parser, $target))
            ->then
                ->mock($parser)
                    ->call('parse')->withIdenticalArguments('baz')->once()
                ->mock($target)
                    ->call('compile')->withIdenticalArguments($document, $outputFile)->once();
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
