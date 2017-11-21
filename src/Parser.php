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

    const HEADER_GROUP    = 0;
    const HEADER_RESOURCE = 1;
    const HEADER_UNKNOWN  = 2;

    protected static $_markdownParser = null;
    protected $_state                 = null;
    protected $_walker                = null;
    protected $_currentDocument       = null;
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

        $this->parseStructure();

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

        if (null !== $event) {
            $this->debug('?? ' . ($event->isEntering() ? 'Entering' : 'Leaving') . ' a ' . get_class($event->getNode()) . ' node' . "\n");
        } else {
            $this->debug('?? null');
        }

        $this->debug('<< ' . ($this->_currentIsEntering ? 'Entering' : 'Leaving') . ' a ' . get_class($this->_currentNode) . ' node' . "\n");

        $this->_walker->resumeAt($this->_currentNode, $this->_currentIsEntering);
        $this->_walker->next(); // move to the state of the current node.

        return $event;
    }

    protected function parseStructure()
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        while ($event = $this->next(true)) {
            $node       = $event->getNode();
            $isEntering = $event->isEntering();

            $this->debug('%% state = ' . $this->_state . "\n");

            // End of the document.
            if (self::STATE_AFTER_DOCUMENT === $this->_state) {
                return;
            }
            // Beginning of the document.
            elseif (self::STATE_BEFORE_DOCUMENT === $this->_state &&
                $node instanceof Block\Document && $isEntering) {
                $this->parseDocument($node);
            }
            // Entering heading level 1: Either group section, resource
            // section, or a document description.
            elseif ($node instanceof Block\Heading && $isEntering &&
                    1 === $node->getLevel()) {
                $this->parseDescriptionOrGroupOrResource($node);
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

    protected function getHeaderType(string $headerContent, &$matches = []): int
    {
        // Resource group section.
        if (0 !== preg_match('/^Group ([^\[\]\(\)]+)/', $headerContent, $matches)) {
            return self::HEADER_GROUP;
        }

        // Resource section.
        if (0 !== preg_match('/^(?<name>[^\[]+)\[(?<uriTemplate>[^\]]+)\]/', $headerContent, $matches)) {
            return self::HEADER_RESOURCE;
        }

        // API name.
        return self::HEADER_UNKNOWN;
    }

    protected function parseDescriptionOrGroupOrResource(Block\Heading $node)
    {
        $this->debug('>> ' . __FUNCTION__ . "\n");

        $headerContent = trim($node->getStringContent()) ?? '';

        switch ($this->getHeaderType($headerContent, $matches)) {
            case self::HEADER_GROUP:
                $this->_state = self::STATE_IN_GROUP;

                $group       = new IR\Group();
                $group->name = $matches[1];

                $this->_currentDocument->groups[] = $group;

                $level = $node->getLevel();

                while ($event = $this->peek()) {
                    $nextNode   = $event->getNode();
                    $isEntering = $event->isEntering();

                    if ($nextNode instanceof Block\Heading && $isEntering) {
                        if ($nextNode->getLevel() <= $level) {
                            return;
                        }

                        $this->next();

                        $nextHeaderContent = trim($nextNode->getStringContent()) ?? '';

                        if (self::HEADER_RESOURCE === $this->getHeaderType($nextHeaderContent, $nextMatches)) {
                            $this->parseResource(
                                $nextNode,
                                $group,
                                $nextMatches['name'],
                                $nextMatches['uriTemplate']
                            );
                        }
                    } else {
                        $this->next();
                    }
                }

                break;

            case self::HEADER_RESOURCE:
                $this->parseResource(
                    $node,
                    $this->_currentDocument,
                    $matches['name'],
                    $matches['uriTemplate']
                );

                break;

            case self::HEADER_UNKNOWN:
                if (empty($this->_currentDocument->apiName)) {
                    $this->_currentDocument->apiName = $headerContent;
                }

                break;
        }
    }

    protected function parseResource(Block\Heading $resourceNode, $parent, string $name, string $uriTemplate)
    {
        $this->_state = self::STATE_IN_RESOURCE;

        $resource              = new IR\Resource();
        $resource->name        = trim($name);
        $resource->uriTemplate = strtolower(trim($uriTemplate));

        $parent->resources[] = $resource;
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
