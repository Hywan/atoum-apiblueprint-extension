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
    protected $_walker                = null;
    protected $_currentNode           = null;
    protected $_currentIsEntering     = false;

    public function parse(string $apib): IR\Document
    {
        $markdownParser = static::getMarkdownParser();
        $this->_walker  = $markdownParser->parse($apib)->walker();

        //while ($event = $this->next()); exit;

        return $this->parseNext();
    }

    protected function parseNext()
    {
        while ($event = $this->next(true)) {
            $node       = $event->getNode();
            $isEntering = $event->isEntering();

            switch (true) {
                case $node instanceof Block\Document && $isEntering:
                    return $this->parseDocument(new IR\Document(), $node);
            }
        }
    }

    protected function parseDocument(IR\Document $document, $node): IR\Document
    {
        $event = $this->peek();

        // The document is empty.
        if ($event->getNode() instanceof Block\Document && false === $event->isEntering()) {
            return $document;
        }

        // The document might have metadata.
        if ($event->getNode() instanceof Block\Paragraph && true === $event->isEntering()) {
            $this->next();

            do {
                $event = $this->next();

                if ($event->getNode() instanceof Block\Paragraph && false === $event->isEntering()) {
                    break;
                }

                if ($event->getNode() instanceof Inline\Text &&
                    0 !== preg_match('/^([^:]+):(.*)$/', $event->getNode()->getContent(), $match)) {
                    $document->metadata[mb_strtolower(trim($match[1]))] = trim($match[2]);
                }
            } while(true);
        }

        $this->parseNext();

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

    protected function next(bool $expectEOF = false)
    {
        $event = $this->_walker->next();

        if (null === $event) {
            if (false === $expectEOF) {
                throw new Exception\ParserEOF('End of the document has been reached unexpectedly.');
            } else {
                return null;
            }
        }

        //echo ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";

        $this->_currentNode       = $event->getNode();
        $this->_currentIsEntering = $event->isEntering();

        return $event;
    }

    protected function peek()
    {
        $event = $this->_walker->next();

        //echo '?? ' . ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n";

        $this->_walker->resumeAt($this->_currentNode, $this->_currentIsEntering);

        return $event;
    }
}
