<?php

declare(strict_types=1);

namespace atoum\apiblueprint\IntermediateRepresentation;

class Resource
{
    /**
     * Resource name.
     */
    public $name          = '';

    /**
     * Request method.
     */
    public $requestMethod = 'get';

    /**
     * URI template.
     */
    public $uriTemplate   = '';
}
