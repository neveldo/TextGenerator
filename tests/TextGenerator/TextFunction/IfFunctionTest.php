<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\IfFunction;

class IfFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private IfFunction $ifFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->ifFunction = new IfFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->ifFunction->execute([], []);
    }

    public function testWithOneArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->ifFunction->execute([''], ['']);
    }

    public function testConditionTrue(): void
    {
        $result = $this->ifFunction->execute(['2 > 1', 'ok', 'notok'], ['2 > 1', 'ok', 'notok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalse(): void
    {
        $result = $this->ifFunction->execute(['2 < 1', 'ok', 'notok'], ['2 < 1', 'ok', 'notok']);
        $this->assertEquals('notok', $result);
    }

    public function testConditionTrueWithNoElse(): void
    {
        $result = $this->ifFunction->execute(['2 > 1', 'ok'], ['2 > 1', 'ok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalseWithNoElse(): void
    {
        $result = $this->ifFunction->execute(['2 < 1', 'ok'], ['2 < 1', 'ok']);
        $this->assertEquals('[EMPTY]', $result);
    }
}
