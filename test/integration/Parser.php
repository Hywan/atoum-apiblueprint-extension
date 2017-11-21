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
    /*
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
                    $resource              = new IR\Resource(),
                    $resource->name        = 'Foo Bar',
                    $resource->uriTemplate = '/foo/bar',

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
                    $resource1              = new IR\Resource(),
                    $resource1->name        = 'Foo Bar',
                    $resource1->uriTemplate = '/foo/bar',

                    $resource2              = new IR\Resource(),
                    $resource2->name        = 'Baz Qux',
                    $resource2->uriTemplate = '/baz/qux',

                    $document              = new IR\Document(),
                    $document->resources[] = $resource1,
                    $document->resources[] = $resource2
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_one_empty_resource_within_a_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '# Group A' . "\n" . '## Foo Bar [/foo/bar]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $resource              = new IR\Resource(),
                    $resource->name        = 'Foo Bar',
                    $resource->uriTemplate = '/foo/bar',

                    $group              = new IR\Group(),
                    $group->name        = 'A',
                    $group->resources[] = $resource,

                    $document           = new IR\Document(),
                    $document->groups[] = $group
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_many_empty_resources_within_a_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Group A' . "\n" .
                    '## Foo Bar [/foo/bar]' . "\n" .
                    '## Baz Qux [/baz/qux]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $resource1              = new IR\Resource(),
                    $resource1->name        = 'Foo Bar',
                    $resource1->uriTemplate = '/foo/bar',

                    $resource2              = new IR\Resource(),
                    $resource2->name        = 'Baz Qux',
                    $resource2->uriTemplate = '/baz/qux',

                    $group              = new IR\Group(),
                    $group->name        = 'A',
                    $group->resources[] = $resource1,
                    $group->resources[] = $resource2,

                    $document           = new IR\Document(),
                    $document->groups[] = $group
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_empty_resources_and_groups_at_different_levels()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Resource 1 [/resource/1]' . "\n" .
                    '# Group A' . "\n" .
                    '## Resource 2 [/group/a/resource/2]' . "\n" .
                    '## Resource 3 [/group/a/resource/3]' . "\n" .
                    '# Group B' . "\n" .
                    '## Resource 4 [/group/b/resource/4]' . "\n" .
                    '# Group C' . "\n" .
                    '# Group D' . "\n" .
                    '## Resource 5 [/group/d/resource/5]' . "\n" .
                    '# Resource 6 [/resource/6]' . "\n" .
                    '# Group E' . "\n" .
                    '# Resource 7 [/resource/7]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $resource1              = new IR\Resource(),
                    $resource1->name        = 'Resource 1',
                    $resource1->uriTemplate = '/resource/1',

                    $resource2              = new IR\Resource(),
                    $resource2->name        = 'Resource 2',
                    $resource2->uriTemplate = '/group/a/resource/2',

                    $resource3              = new IR\Resource(),
                    $resource3->name        = 'Resource 3',
                    $resource3->uriTemplate = '/group/a/resource/3',

                    $resource4              = new IR\Resource(),
                    $resource4->name        = 'Resource 4',
                    $resource4->uriTemplate = '/group/b/resource/4',

                    $resource5              = new IR\Resource(),
                    $resource5->name        = 'Resource 5',
                    $resource5->uriTemplate = '/group/d/resource/5',

                    $resource6              = new IR\Resource(),
                    $resource6->name        = 'Resource 6',
                    $resource6->uriTemplate = '/resource/6',

                    $resource7              = new IR\Resource(),
                    $resource7->name        = 'Resource 7',
                    $resource7->uriTemplate = '/resource/7',

                    $groupA              = new IR\Group(),
                    $groupA->name        = 'A',
                    $groupA->resources[] = $resource2,
                    $groupA->resources[] = $resource3,

                    $groupB              = new IR\Group(),
                    $groupB->name        = 'B',
                    $groupB->resources[] = $resource4,

                    $groupC       = new IR\Group(),
                    $groupC->name = 'C',

                    $groupD              = new IR\Group(),
                    $groupD->name        = 'D',
                    $groupD->resources[] = $resource5,

                    $groupE       = new IR\Group(),
                    $groupE->name = 'E',

                    $document              = new IR\Document(),
                    $document->resources[] = $resource1,
                    $document->resources[] = $resource6,
                    $document->resources[] = $resource7,
                    $document->groups[]    = $groupA,
                    $document->groups[]    = $groupB,
                    $document->groups[]    = $groupC,
                    $document->groups[]    = $groupD,
                    $document->groups[]    = $groupE
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_empty_resource_at_level_2_without_a_parent_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  = '## Resource 1 [/resource/1]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->object($result)
                    ->isEqualTo(new IR\Document());
    }

    public function test_one_empty_action_in_a_resource_in_a_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Group A' . "\n" .
                    '## Resource 1 [/group/a/resource/1]' . "\n" .
                    '### Action Foo Bar [GET]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $action                = new IR\Action(),
                    $action->name          = 'Action Foo Bar',
                    $action->requestMethod = 'GET',

                    $resource              = new IR\Resource(),
                    $resource->name        = 'Resource 1',
                    $resource->uriTemplate = '/group/a/resource/1',
                    $resource->actions[]   = $action,

                    $group              = new IR\Group(),
                    $group->name        = 'A',
                    $group->resources[] = $resource,

                    $document           = new IR\Document(),
                    $document->groups[] = $group
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_one_empty_action_in_a_resource()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Resource 1 [/group/a/resource/1]' . "\n" .
                    '## Action Foo Bar [GET]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $action                = new IR\Action(),
                    $action->name          = 'Action Foo Bar',
                    $action->requestMethod = 'GET',

                    $resource              = new IR\Resource(),
                    $resource->name        = 'Resource 1',
                    $resource->uriTemplate = '/group/a/resource/1',
                    $resource->actions[]   = $action,

                    $document              = new IR\Document(),
                    $document->resources[] = $resource
                )
                ->object($result)
                    ->isEqualTo($document);
    }

    public function test_many_empty_actions_in_a_resource_in_a_group()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Group A' . "\n" .
                    '## Resource 1 [/group/a/resource/1]' . "\n" .
                    '### Action Foo Bar [GET]' . "\n" .
                    '### Action Baz Qux [HELLO]'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $action1                = new IR\Action(),
                    $action1->name          = 'Action Foo Bar',
                    $action1->requestMethod = 'GET',

                    $action2                = new IR\Action(),
                    $action2->name          = 'Action Baz Qux',
                    $action2->requestMethod = 'HELLO',

                    $resource              = new IR\Resource(),
                    $resource->name        = 'Resource 1',
                    $resource->uriTemplate = '/group/a/resource/1',
                    $resource->actions[]   = $action1,
                    $resource->actions[]   = $action2,

                    $group              = new IR\Group(),
                    $group->name        = 'A',
                    $group->resources[] = $resource,

                    $document           = new IR\Document(),
                    $document->groups[] = $group
                )
                ->object($result)
                    ->isEqualTo($document);
    }
    */

    public function test_one_complete_action_in_a_resource()
    {
        $this
            ->given(
                $parser = new SUT(),
                $datum  =
                    '# Resource 1 [/group/a/resource/1]' . "\n" .
                    '## Action Foo Bar [GET]' . "\n" .
                    '+ Request A (media/type)'
            )
            ->when($result = $parser->parse($datum))
            ->then
                ->let(
                    $action                = new IR\Action(),
                    $action->name          = 'Action Foo Bar',
                    $action->requestMethod = 'GET',

                    $resource              = new IR\Resource(),
                    $resource->name        = 'Resource 1',
                    $resource->uriTemplate = '/group/a/resource/1',
                    $resource->actions[]   = $action,

                    $document              = new IR\Document(),
                    $document->resources[] = $resource
                )
                ->object($result)
                    ->isEqualTo($document);
    }
}
