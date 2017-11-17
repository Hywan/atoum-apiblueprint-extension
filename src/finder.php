<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

class finder extends \CallbackFilterIterator
{
    public function __construct()
    {
        parent::__construct(
            new \AppendIterator(),
            function (\SplFileInfo $current): bool {
                return '.apib' === substr($current->getBasename(), -5, 5);
            }
        );
    }
}
