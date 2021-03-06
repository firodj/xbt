<?php
namespace Xbt;

use Mockery;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_include_doctype_when_doctype_attribute_is_set_to_true()
    {
        $node = Mockery::mock(Node::class)->makePartial();
        $node->shouldReceive('render')->andReturn('');

        $children = Mockery::mock(NodeList::class, [[$node]])->makePartial();
        $children->shouldReceive('render')->andReturn('foobar');

        $blockName = Mockery::mock(StringNode::class, ['"for_the_win"'])->makePartial();
        $blockName->shouldReceive('render')->andReturn('"for_the_win"');

        $blockAttributes = Mockery::mock(TagAttributes::class, [[':name' => $blockName]])->makePartial();
        $blockAttributes->shouldReceive('render')->andReturn('name="for_the_win"');

        $p = new TagNode(':p', new TagAttributes, new NodeList);

        $blockChildren = Mockery::mock(NodeList::class, [[$p]])->makePartial();
        $blockChildren->shouldReceive('render')->andReturn('<p />');

        $blockNode = Mockery::mock(BlockNode::class, [$blockAttributes, $blockChildren])->makePartial();
        $blocks = ['for_the_win' => $blockNode];

        $doctype = Mockery::mock(StringNode::class, ['"true"'])->makePartial();

        $attributes = Mockery::mock(TagAttributes::class, [[':doctype' => $doctype]])->makePartial();

        $template = new Template($attributes, $children, $blocks);

        $class = '__xbt_' . md5('foobar_with_doctype');

        $expected =<<<EXPECTED
return new \\Xbt\TemplateRuntime(
    null,
    function(\$__this, \$__params = []) {
        return <x:doctype>foobar</x:doctype>;
    },
    [
        'for_the_win' => function(\$__this, \$__params = []) {
            return <x:frag><p /></x:frag>;
        },
    ]
);
EXPECTED;

        $this->assertEquals($expected, $template->compile());

    }

    /**
     * @expectedException Xbt\SyntaxError
     */
    public function test_include_doctype_when_doctype_attribute_is_set_to_something_other_than_a_literal_true_or_false_string()
    {
        $node = Mockery::mock(Node::class)->makePartial();
        $node->shouldReceive('render')->andReturn('');

        $children = Mockery::mock(NodeList::class, [[$node]])->makePartial();
        $children->shouldReceive('render')->andReturn('foobar');

        $blockName = Mockery::mock(StringNode::class, ['"for_the_win"'])->makePartial();
        $blockName->shouldReceive('render')->andReturn('"for_the_win"');

        $blockAttributes = Mockery::mock(TagAttributes::class, [[':name' => $blockName]])->makePartial();
        $blockAttributes->shouldReceive('render')->andReturn('name="for_the_win"');

        $p = new TagNode(':p', new TagAttributes, new NodeList);

        $blockChildren = Mockery::mock(NodeList::class, [[$p]])->makePartial();
        $blockChildren->shouldReceive('render')->andReturn('<p />');

        $blockNode = Mockery::mock(BlockNode::class, [$blockAttributes, $blockChildren])->makePartial();
        $blocks = ['for_the_win' => $blockNode];

        $doctype = Mockery::mock(DelimitedExpressionNode::class, ['{"true"}'])->makePartial();

        $attributes = Mockery::mock(TagAttributes::class, [[':doctype' => $doctype]])->makePartial();

        $template = new Template($attributes, $children, $blocks);
        $expected =<<<EXPECTED
return new \\Xbt\TemplateRuntime(
    null,
    function(\$__this, \$__params = []) {
        return <x:doctype>foobar</x:doctype>;
    },
    [
        'for_the_win' => function(\$__this, \$__params = []) {
            return <x:frag><p /></x:frag>;
        },
    ]
);
EXPECTED;

        $this->assertEquals($expected, $template->compile());
    }


    public function test_compile_outputs_template_runtime_instance()
    {

        $node = Mockery::mock(Node::class)->makePartial();
        $node->shouldReceive('render')->andReturn('');

        $children = Mockery::mock(NodeList::class, [[$node]])->makePartial();
        $children->shouldReceive('render')->andReturn('foobar');

        $blockName = Mockery::mock(StringNode::class, ['"for_the_win"'])->makePartial();
        $blockName->shouldReceive('render')->andReturn('"for_the_win"');

        $blockAttributes = Mockery::mock(TagAttributes::class, [[':name' => $blockName]])->makePartial();
        $blockAttributes->shouldReceive('render')->andReturn('name="for_the_win"');

        $p = new TagNode(':p', new TagAttributes, new NodeList);

        $blockChildren = Mockery::mock(NodeList::class, [[$p]])->makePartial();
        $blockChildren->shouldReceive('render')->andReturn('<p />');

        $blockNode = Mockery::mock(BlockNode::class, [$blockAttributes, $blockChildren])->makePartial();
        $blocks = ['for_the_win' => $blockNode];

        $attributes = Mockery::mock(TagAttributes::class, [[]])->makePartial();

        $template = new Template($attributes, $children, $blocks);

        $expected =<<<EXPECTED
return new \\Xbt\TemplateRuntime(
    null,
    function(\$__this, \$__params = []) {
        return <x:frag>foobar</x:frag>;
    },
    [
        'for_the_win' => function(\$__this, \$__params = []) {
            return <x:frag><p /></x:frag>;
        },
    ]
);
EXPECTED;

        $this->assertEquals($expected, $template->compile());
    }

    public function test_compile_outputs_template_runtime_instance_with_parent() {

        $node = Mockery::mock(Node::class)->makePartial();
        $node->shouldReceive('render')->andReturn('');

        $children = Mockery::mock(NodeList::class, [[$node]])->makePartial();
        $children->shouldReceive('render')->andReturn('foobar');

        $blockName = Mockery::mock(StringNode::class, ['"for_the_win"'])->makePartial();
        $blockName->shouldReceive('render')->andReturn('"for_the_win"');

        $blockAttributes = Mockery::mock(TagAttributes::class, [[':name' => $blockName]])->makePartial();
        $blockAttributes->shouldReceive('render')->andReturn('name="for_the_win"');

        $p = new TagNode(':p', new TagAttributes, new NodeList);

        $blockChildren = Mockery::mock(NodeList::class, [[$p]])->makePartial();
        $blockChildren->shouldReceive('render')->andReturn('<p />');

        $blockNode = Mockery::mock(BlockNode::class, [$blockAttributes, $blockChildren])->makePartial();
        $blocks = ['for_the_win' => $blockNode];

        $attributes = Mockery::mock(TagAttributes::class, [[':extends' => new StringNode('"layouts.mobile.root"')]])->makePartial();

        $template = new Template($attributes, $children, $blocks);

        $expected =<<<EXPECTED
return new \\Xbt\TemplateRuntime(
    app('xbt.compiler')->compileExtends('layouts.mobile.root'),
    function(\$__this, \$__params = []) {
        return <x:frag>foobar</x:frag>;
    },
    [
        'for_the_win' => function(\$__this, \$__params = []) {
            return <x:frag><p /></x:frag>;
        },
    ]
);
EXPECTED;

        $this->assertEquals($expected, $template->compile());

    }

    /**
     * @expectedException Xbt\SyntaxError
     */
    public function test_extends_attribute_must_be_a_string_node()
    {
        $attributes = new TagAttributes([':extends' => new DelimitedExpressionNode('{1}')]);
        $children   = new NodeList([]);
        $blocks     = [];
        $template   = new Template($attributes, $children, $blocks);
    }
}

