<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Payload
{
    /**
     * Raw body.
     */
    public $body = '';

    /**
     * Hashmap of headers, where the keys are the header names, and the values
     * are the header values.
     */
    public $headers = [];

    public $schema = '';
}
