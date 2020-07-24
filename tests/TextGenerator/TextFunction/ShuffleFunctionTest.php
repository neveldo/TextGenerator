<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\ShuffleFunction;

class ShuffleFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setup(): void {
        $this->tagReplacer = new TagReplacer();
        $this->function = new ShuffleFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([], []);
    }

    public function testWithOneArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([','], [',']);
    }

    public function testWithOneString()
    {
        $result = $this->function->execute([',', 'test'], [',', 'test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoStrings()
    {
        $result = $this->function->execute([',', 'test1', 'test2'], [',', 'test1', 'test2']);
        $this->assertContains($result, ['test1,test2', 'test2,test1']);
    }

    public function testWithTwoStringsAndEmptySeparator()
    {
        $result = $this->function->execute(['', 'test1', 'test2'], ['', 'test1', 'test2']);
        $this->assertContains($result, ['test1test2', 'test2test1']);
    }

    public function testWithOneEmptyString()
    {
        $result = $this->function->execute([',', 'test1', ''], [',', 'test1', '']);
        $this->assertEquals('test1', $result);
    }

    public function testWithStringThatContainsEmptyTag()
    {
        $result = $this->function->execute([',', 'test1' . $this->tagReplacer->getEmptyTag(), 'test2'], [',', 'test1' . $this->tagReplacer->getEmptyTag(), 'test2']);
        $this->assertEquals('test2', $result);
    }
}