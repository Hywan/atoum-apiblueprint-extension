<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Response implements Message
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
