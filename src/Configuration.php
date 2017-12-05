<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;
use RuntimeException;

class Configuration implements atoum\extension\configuration
{
    protected $_jsonSchemaMountPoints = [];

    public function mountJsonSchemaDirectory(string $rootName, string $directory)
    {
        $_directory = realpath($directory);

        if (false === $_directory || false === is_dir($_directory)) {
            throw new RuntimeException(
                'Try to mount the `' . $directory . '` directory ' .
                'as `' . $rootName . '`, but it does not exist.'
            );
        }

        $this->_jsonSchemaMountPoints[$rootName] = $_directory;
    }

    public function unmountJsonSchemaDirectory(string $rootName)
    {
        unset($this->_router[$rootName]);
    }

    public function getJsonSchemaMountPoints(): array
    {
        return $this->_jsonSchemaMountPoints;
    }

    public function serialize()
    {
        return [
            'jsonSchemaMountPoints' => $this->getJsonSchemaMountPoints()
        ];
    }

    public static function unserialize(array $configuration)
    {
        $self = new static();

        if (isset($configuration['jsonSchemaMountPoints'])) {
            $self->_jsonSchemaMountPoints = $configuration['jsonSchemaMountPoints'];
        }

        return $self;
    }
}
