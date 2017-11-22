<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum\writers\file;

class Target
{
    public function compile(IntermediateRepresentation\Document $document, file $outputFile)
    {
        $outputFile->write('<?php' . "\n\n");
    }
}
