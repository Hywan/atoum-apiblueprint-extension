<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

class parser
{
    public function parse(string $apib): intermediateRepresentation\document
    {
        return new intermediateRepresentation\document();
    }
}
