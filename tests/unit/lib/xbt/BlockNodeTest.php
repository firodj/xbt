<?hh

namespace Lib\xbt;

use Mockery as m;

class BlockNodeTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException Lib\xbt\SyntaxError
     */
    public function test_block_name_must_not_contain_dots()
    {
        $attributes = m::mock(TagAttributes::class, [Map<string, ExpressionNode> {':name' => m::mock(StringNode::class, ['"foo.bar"'])->makePartial()}])->makePartial();

        $children = m::mock(NodeList::class, [Vector<Node> {}])->makePartial();

        $block = new BlockNode($attributes, $children);
    }

    /**
     * @expectedException Lib\xbt\SyntaxError
     */
    public function test_block_name_must_not_contain_dashes()
    {
        $attributes = m::mock(TagAttributes::class, [Map<string, ExpressionNode> {':name' => m::mock(StringNode::class, ['"foo-bar"'])->makePartial()}])->makePartial();

        $children = m::mock(NodeList::class, [Vector<Node> {}])->makePartial();

        $block = new BlockNode($attributes, $children);
    }

    /**
     * @expectedException Lib\xbt\SyntaxError
     */
    public function test_block_name_must_not_contain_anything_other_than_alphanumeric_and_underscores()
    {
        $attributes = m::mock(TagAttributes::class, [Map<string, ExpressionNode> {':name' => m::mock(StringNode::class, ['"^foo@bar!"'])->makePartial()}])->makePartial();

        $children = m::mock(NodeList::class, [Vector<Node> {}])->makePartial();

        $block = new BlockNode($attributes, $children);
    }

    public function test_render_renders_the_block_call()
    {
        $attributes = m::mock(TagAttributes::class, [Map<string, ExpressionNode> {':name' => m::mock(StringNode::class, ['"foo_bar"'])->makePartial()}])->makePartial();

        $children = m::mock(NodeList::class, [Vector<Node> {}])->makePartial();

        $block = new BlockNode($attributes, $children);

        $expected =<<<'EXPECTED'
{$this->block_foo_bar()}
EXPECTED;

        $this->assertEquals($expected, $block->render());
    }

    public function test_renderBody_renders_the_body_of_the_block()
    {
        $attributes = m::mock(TagAttributes::class, [Map<string, ExpressionNode> {':name' => m::mock(StringNode::class, ['"foo_bar"'])->makePartial()}])->makePartial();

        $p = m::mock(Node::class)->makePartial();
        $p->shouldReceive('render')->andReturn('<p>This is just a paragraph</p>');

        $children = m::mock(NodeList::class, [Vector<Node> {$p}])->makePartial();

        $block = new BlockNode($attributes, $children);

        $expected =<<<'EXPECTED'
    public function block_foo_bar()
    {
        extract($this->params);
        return <x:frag><p>This is just a paragraph</p></x:frag>;
    }
EXPECTED;

        $this->assertEquals($expected, $block->renderBody());
    }
}
