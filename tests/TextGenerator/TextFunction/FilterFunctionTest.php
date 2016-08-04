<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\FilterFunction;

class FilterFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new FilterFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->function->execute([]);
    }

    public function testWithOneArgument()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->function->execute(['round']);
    }

    public function testRound()
    {
        $result = $this->function->execute(['round', '3.44444']);
        $this->assertEquals(3, $result);
    }

    public function testRound2()
    {
        $result = $this->function->execute(['round', '3.44444', 1]);
        $this->assertEquals(3.4, $result);
    }

    public function testUnexistantFilter()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $result = $this->function->execute(['unexistant', '3.44444', 1]);
    }

    public function testTooManyParams()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $result = $this->function->execute(['round', '3.44444', 1, '3.44444', 1, '3.44444', 1, '3.44444', 1]);
    }
}