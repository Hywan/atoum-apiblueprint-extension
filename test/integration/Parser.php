<?php

declare(strict_types=1);

namespace atoum\apiblueprint\test\integration;

use League\CommonMark\Block\Element as Block;
use League\CommonMark\Inline\Element as Inline;
use atoum\apiblueprint as LUT;
use atoum\apiblueprint\IntermediateRepresentation as IR;
use atoum\apiblueprint\Parser as SUT;
use mageekguy\atoum\test;

class Parser extends test
{
    public function test_empty_document()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = ''
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->object($result)
                    ->isEqualTo(new IR\Document());
    }

    public function test_metadata()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = 'FORMAT: 1A' . "\n" . 'HOST: https://example.org/'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $document           = new IR\Document(),
                    $document->metadata = [
                        'format' => '1A',
                        'host'   => 'https://example.org/'
                    ]
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_api_name_and_overview_section()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Basic _example_ API' . "\n" .
                    'Welcome to the **ACME Blog** API. This API provides access to the **ACME' . "\n" .
                    'Blog** service.'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $document          = new IR\Document(),
                    $document->apiName = 'Basic _example_ API'
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_one_empty_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '# Group Foo Bar'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $group       = new IR\Group(),
                    $group->name = 'Foo Bar',

                    $document           = new IR\Document(),
                    $document->groups[] = $group
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_many_empty_groups()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '# Group Foo Bar' . "\n" . '# Group Baz Qux'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $group1       = new IR\Group(),
                    $group1->name = 'Foo Bar',

                    $group2       = new IR\Group(),
                    $group2->name = 'Baz Qux',

                    $document           = new IR\Document(),
                    $document->groups[] = $group1,
                    $document->groups[] = $group2
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_one_empty_resource()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '# Foo Bar [/foo/bar]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $resource       = new IR\Resource(),
                    $resource->name = 'Foo Bar',
                    $resource->url  = '/foo/bar',

                    $document              = new IR\Document(),
                    $document->resources[] = $resource
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_many_empty_resources()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '# Foo Bar [/foo/bar]' . "\n" . '# Baz Qux [/baz/qux]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $resource1       = new IR\Resource(),
                    $resource1->name = 'Foo Bar',
                    $resource1->url  = '/foo/bar',

                    $resource2       = new IR\Resource(),
                    $resource2->name = 'Baz Qux',
                    $resource2->url  = '/baz/qux',

                    $document              = new IR\Document(),
                    $document->resources[] = $resource1,
                    $document->resources[] = $resource2
                )
                ->object($result)
                    ->isEqualTo($document);
    }
}
