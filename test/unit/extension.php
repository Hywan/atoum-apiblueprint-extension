<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\unit;

use atoum\apiblueprint\extension as SUT;
use mageekguy\atoum\test;

class extension extends test
{
    public function testAddToRunner()
    {
        $this
            ->given(
                $this->mockGenerator->orphanize('__construct'),
                $runner = new \mock\mageekguy\atoum\runner(),
                $this->calling($runner)->addExtension->doesNothing(),

                $extension = new SUT()
            )
            ->when($result = $extension->addToRunner($runner))
            ->then
                ->object($result)
                    ->isIdenticalTo($extension)
                ->mock($runner)
                    ->call('addExtension')->withIdenticalArguments($extension)->once();
    }
}
