<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\ExprFunction;

class ExprFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private ExprFunction $exprFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->exprFunction = new ExprFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->exprFunction->execute([], []);
    }

    public function testWithTownArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->exprFunction->execute(['', ''], ['', '']);
    }

    public function testSimpleExpression(): void
    {
        $result = $this->exprFunction->execute(['2 + 1'], ['2 + 1']);
        $this->assertEquals('3', $result);
    }
}
