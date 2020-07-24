<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\RandomFunction;

class RandomFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setup(): void {
        $this->tagReplacer = new TagReplacer();
        $this->function = new RandomFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $result = $this->function->execute([], []);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithOneArgument()
    {
        $result = $this->function->execute(['test'], ['test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoArguments()
    {
        $result = $this->function->execute(['test1', 'test2'], ['test1', 'test2']);
        $this->assertContains($result, ['test1', 'test2']);
    }

    public function testWithStringThatContainsEmptyTag()
    {
        $result = $this->function->execute(['test1' . $this->tagReplacer->getEmptyTag(), 'test2'], ['test1' . $this->tagReplacer->getEmptyTag(), 'test2']);
        $this->assertEquals('test2', $result);
    }
}