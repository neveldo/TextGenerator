<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\ChooseFunction;

class ChooseFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private ChooseFunction $chooseFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->chooseFunction = new ChooseFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->chooseFunction->execute([], []);
    }

    public function testWithOneArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->chooseFunction->execute([''], ['']);
    }

    public function testSimpleChoose(): void
    {
        $result = $this->chooseFunction->execute(['2', 'test1','test2', 'test3'], ['2', 'test1','test2', 'test3']);
        $this->assertEquals('test2', $result);
    }

    public function testSimpleChoose2(): void
    {
        $result = $this->chooseFunction->execute(['3', 'test1','test2', 'test3'], ['3', 'test1','test2', 'test3']);
        $this->assertEquals('test3', $result);
    }

    public function testChooseWithUnexistantArg(): void
    {
        $result = $this->chooseFunction->execute(['0', 'test1','test2', 'test3'], ['0', 'test1','test2', 'test3']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testChooseWithUnexistantArg2(): void
    {
        $result = $this->chooseFunction->execute(['7', 'test1','test2', 'test3'], ['7', 'test1','test2', 'test3']);
        $this->assertEquals('[EMPTY]', $result);
    }
}
