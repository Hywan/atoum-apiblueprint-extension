<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;

class test implements \mageekguy\atoum\test
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }
}
