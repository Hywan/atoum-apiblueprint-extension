<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;

class extension implements atoum\extension
{
    public function __construct(atoum\configurator $configurator = null)
    {
        if ($configurator) {
            $parser = $configurator->getScript()->getArgumentsParser();

            $handler = function ($script, $argument, $values) {
                $script->getRunner()->addTestsFromDirectory(dirname(__DIR__) . '/test/');
            };

            $parser
                ->addHandler($handler, ['--test-ext'])
                ->addHandler($handler, ['--test-it']);
        }
    }

    public function addToRunner(atoum\runner $runner)
    {
        $runner->addExtension($this);

        return $this;
    }

    public function setRunner(atoum\runner $runner)
    {
        return $this;
    }

    public function setTest(atoum\test $test)
    {
        return $this;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
    }
}
