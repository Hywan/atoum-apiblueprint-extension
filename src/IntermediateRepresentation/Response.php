<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Response
{
    /**
     * Status code as an integer.
     */
    public $statusCode = 200;

    /**
     * Media type.
     */
    public $mediaType  = '';
}
