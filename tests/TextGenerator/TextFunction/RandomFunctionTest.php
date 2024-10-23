<?php

namespace Neveldo\TextGenerator\Tag;

use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\RandomFunction;

class RandomFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private RandomFunction $randomFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->randomFunction = new RandomFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $result = $this->randomFunction->execute([], []);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithOneArgument(): void
    {
        $result = $this->randomFunction->execute(['test'], ['test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoArguments(): void
    {
        $result = $this->randomFunction->execute(['test1', 'test2'], ['test1', 'test2']);
        $this->assertContains($result, ['test1', 'test2']);
    }

    public function testWithStringThatContainsEmptyTag(): void
    {
        $result = $this->randomFunction->execute(['test1' . $this->tagReplacer->getEmptyTag(), 'test2'], ['test1' . $this->tagReplacer->getEmptyTag(), 'test2']);
        $this->assertEquals('test2', $result);
    }
}
