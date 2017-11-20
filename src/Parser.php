<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use League\CommonMark;
use League\CommonMark\Block\Element as Block;
use League\CommonMark\Inline\Element as Inline;
use atoum\apiblueprint\IntermediateRepresentation as IR;

class Parser
{
    const STATE_BEFORE_DOCUMENT = 0;
    const STATE_AFTER_DOCUMENT  = 1;
    const STATE_ROOT            = 2;
    const STATE_IN_GROUP        = 3;
    const STATE_IN_RESOURCE     = 4;

    protected static $_markdownParser = null;
    protected $_state                 = null;
    protected $_walker                = null;
    protected $_currentDocument       = null;
    protected $_currentGroup          = null;
    protected $_currentResource       = null;
    protected $_currentNode           = null;
    protected $_currentIsEntering     = false;

    public function parse(string $apib): IR\Document
    {
        $this->_state             = self::STATE_BEFORE_DOCUMENT;
        $this->_currentDocument   = null;
        $this->_currentNode       = null;
        $this->_currentIsEntering = null;

        $markdownParser = static::getMarkdownParser();
        $this->_walker  = $markdownParser->parse($apib)->walker();

        //while ($event = $this->next()); exit;

        $this->parseNext();

        return $this->_currentDocument;
    }

    protected function next(bool $expectEOF = false)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $event = $this->_walker->next();

        if (null === $event) {
            if (false === $expectEOF) {
                throw new Exception\ParserEOF('End of the document has been reached unexpectedly.');
            } else {
                return null;
            }
        }

        $this->debug(($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n");

        $this->_currentNode       = $event->getNode();
        $this->_currentIsEntering = $event->isEntering();

        return $event;
    }

    protected function peek()
    {
        $event = $this->_walker->next();

        $this->debug('?? ' . ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n");
        $this->debug('<< ' . ($this->_currentIsEntering ? 'Entering' : 'Leaving') . ' a ' . get_class($this->_currentNode) . ' node' . "\n");

        $this->_walker->resumeAt($this->_currentNode, $this->_currentIsEntering);
        $this->_walker->next(); // move to the state of the current node.

        return $event;
    }

    protected function parseNext()
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        while ($event = $this->next(true)) {
            $node       = $event->getNode();
            $isEntering = $event->isEntering();

            $this->debug('%% state = ' . $this->_state . "\n");

            switch (true) {
                // End of the document.
                case self::STATE_AFTER_DOCUMENT === $this->_state:
                    return;

                // Beginning of the document.
                case self::STATE_BEFORE_DOCUMENT === $this->_state &&
                     $node instanceof Block\Document && $isEntering:
                    $this->parseDocument($node);

                    break;

                // Entering heading level 1.
                case $node instanceof Block\Heading && $isEntering &&
                     1 === $node->getLevel():
                    $this->parseHeader($node);

                    break;

                // Entering heading level 2.
                case $node instanceof Block\Heading && $isEntering &&
                     2 === $node->getLevel():
                    $this->parseHeader($node);

                    break;
            }
        }
    }

    protected function parseDocument($node)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $this->_currentDocument = new IR\Document();

        $event = $this->peek();

        // The document is empty.
        if ($event->getNode() instanceof Block\Document && false === $event->isEntering()) {
            $this->_state = self::STATE_AFTER_DOCUMENT;

            return;
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
                    $this->_currentDocument->metadata[mb_strtolower(trim($match[1]))] = trim($match[2]);
                }
            } while(true);
        }

        $this->_state = self::STATE_ROOT;

        return;
    }

    protected function parseHeader($node)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $headerContent = trim($node->getStringContent()) ?? '';

        // Resource group section.
        if (0 !== preg_match('/^Group ([^\[\]\(\)]+)/', $headerContent, $match)) {
            $this->_currentGroup       = new IR\Group();
            $this->_currentGroup->name = $match[1];

            $this->_currentDocument->groups[] = $this->_currentGroup;

            $this->_state           = self::STATE_IN_GROUP;
            $this->_currentResource = null;

        }
        // Resource section.
        elseif (0 !== preg_match('/^([^\[]+)\[([^\]]+)\]/', $headerContent, $match)) {
            $this->_state = self::STATE_IN_RESOURCE;

            $this->_currentResource              = new IR\Resource();
            $this->_currentResource->name        = trim($match[1]);
            $this->_currentResource->uriTemplate = strtolower(trim($match[2]));

            if (2 === $node->getLevel()) {
                $this->_currentGroup->resources[] = $this->_currentResource;
            } else {
                $this->_currentDocument->resources[] = $this->_currentResource;
            }
        }
        // API name.
        else {
            $this->_currentDocument->apiName = $headerContent;
        }
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

    private function debug(string $message)
    {
        //echo $message;
    }
}
