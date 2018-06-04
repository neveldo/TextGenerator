<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\ExprFunction;

class ExprFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new ExprFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([], []);
    }

    public function testWithTownArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute(['', ''], ['', '']);
    }

    public function testSimpleExpression()
    {
        $result = $this->function->execute(['2 + 1'], ['2 + 1']);
        $this->assertEquals('3', $result);
    }
}