<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\ChooseFunction;

class ChooseFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setup(): void {
        $this->tagReplacer = new TagReplacer();
        $this->function = new ChooseFunction($this->tagReplacer);
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

    public function testSimpleChoose()
    {
        $result = $this->function->execute([2, 'test1','test2', 'test3'], [2, 'test1','test2', 'test3']);
        $this->assertEquals('test2', $result);
    }

    public function testSimpleChoose2()
    {
        $result = $this->function->execute(['3', 'test1','test2', 'test3'], ['3', 'test1','test2', 'test3']);
        $this->assertEquals('test3', $result);
    }

    public function testChooseWithUnexistantArg()
    {
        $result = $this->function->execute([0, 'test1','test2', 'test3'], [0, 'test1','test2', 'test3']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testChooseWithUnexistantArg2()
    {
        $result = $this->function->execute([7, 'test1','test2', 'test3'], [7, 'test1','test2', 'test3']);
        $this->assertEquals('[EMPTY]', $result);
    }
}