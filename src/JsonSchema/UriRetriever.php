<?php

declare(strict_types=1);

namespace atoum\apiblueprint\JsonSchema;

use JsonSchema\Uri\Retrievers\FileGetContents;

class UriRetriever extends FileGetContents
{
    protected $_router = [];

    public function retrieve($uri)
    {
        $parsed = parse_url($uri);
        $scheme = $parsed['scheme'] ?? '';
        $root   = $parsed['host'] ?? '';
        $path   = $parsed['path'] ?? '';

        if ('json-schema' === $scheme &&
            !empty($root) &&
            !empty($path) &&
            isset($this->_router[$root]) &&
            true === file_exists($this->_router[$root] . $path)) {
            return parent::retrieve($this->_router[$root] . $path);
        }

        return parent::retrieve($uri);
    }

    public function mount(string $rootName, string $rootPath)
    {
        $this->_router[$rootName] = $rootPath . DIRECTORY_SEPARATOR;
    }

    public function unmount(string $rootName)
    {
        unset($this->_router[$rootName]);
    }

    public function get()
    {
        return $this->_router;
    }
}
