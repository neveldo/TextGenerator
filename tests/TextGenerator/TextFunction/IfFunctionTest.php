<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\IfFunction;

class IfFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new IfFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->function->execute([]);
    }

    public function testWithOneArgument()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->function->execute(['']);
    }

    public function testConditionTrue()
    {
        $result = $this->function->execute(['2 > 1', 'ok', 'notok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalse()
    {
        $result = $this->function->execute(['2 < 1', 'ok', 'notok']);
        $this->assertEquals('notok', $result);
    }

    public function testConditionTrueWithNoElse()
    {
        $result = $this->function->execute(['2 > 1', 'ok']);
        $this->assertEquals('ok', $result);
    }

    public function testConditionFalseWithNoElse()
    {
        $result = $this->function->execute(['2 < 1', 'ok']);
        $this->assertEquals('', $result);
    }
}