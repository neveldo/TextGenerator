<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\IfFunction;

class IfFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setup(): void {
        $this->tagReplacer = new TagReplacer();
        $this->function = new IfFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([], []);
    }

    public function testWithOneArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([''], ['']);
    }

    public function testConditionTrue()
    {
        $result = $this->function->execute(['2 > 1', 'ok', 'notok'], ['2 > 1', 'ok', 'notok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalse()
    {
        $result = $this->function->execute(['2 < 1', 'ok', 'notok'], ['2 < 1', 'ok', 'notok']);
        $this->assertEquals('notok', $result);
    }

    public function testConditionTrueWithNoElse()
    {
        $result = $this->function->execute(['2 > 1', 'ok'], ['2 > 1', 'ok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalseWithNoElse()
    {
        $result = $this->function->execute(['2 < 1', 'ok'], ['2 < 1', 'ok']);
        $this->assertEquals('[EMPTY]', $result);
    }
}