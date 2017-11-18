<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use League\CommonMark;
use League\CommonMark\Block\Element as Block;
use League\CommonMark\Inline\Element as Inline;
use atoum\apiblueprint\IntermediateRepresentation as IR;

class Parser
{
    protected static $_markdownParser = null;
    protected $_walker = null;

    public function parse(string $apib): IR\Document
    {
        $markdownParser = static::getMarkdownParser();
        $this->_walker  = $markdownParser->parse($apib)->walker();

        return $this->parseNext();
    }

    protected function parseNext()
    {
        while ($event = $this->_walker->next()) {
            $node = $event->getNode();

            echo ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($node) . ' node' . "\n";

            switch (get_class($node)) {
                case Block\Document::class:
                    return $this->parseDocument(new IR\Document(), $node);
            }
        }
    }

    protected function parseDocument(IR\Document $document, $node): IR\Document
    {
        return $document;
    }

    protected function getMarkdownParser()
    {
        if (null === static::$_markdownParser) {
            static::$_markdownParser = new CommonMark\DocParser(
                CommonMark\Environment::createCommonMarkEnvironment()
            );
        }

        return static::$_markdownParser;
    }
}
