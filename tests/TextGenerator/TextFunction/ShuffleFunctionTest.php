<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\ShuffleFunction;

class ShuffleFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private ShuffleFunction $shuffleFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->shuffleFunction = new ShuffleFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->shuffleFunction->execute([], []);
    }

    public function testWithOneArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->shuffleFunction->execute([','], [',']);
    }

    public function testWithOneString(): void
    {
        $result = $this->shuffleFunction->execute([',', 'test'], [',', 'test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoStrings(): void
    {
        $result = $this->shuffleFunction->execute([',', 'test1', 'test2'], [',', 'test1', 'test2']);
        $this->assertContains($result, ['test1,test2', 'test2,test1']);
    }

    public function testWithTwoStringsAndEmptySeparator(): void
    {
        $result = $this->shuffleFunction->execute(['', 'test1', 'test2'], ['', 'test1', 'test2']);
        $this->assertContains($result, ['test1test2', 'test2test1']);
    }

    public function testWithOneEmptyString(): void
    {
        $result = $this->shuffleFunction->execute([',', 'test1', ''], [',', 'test1', '']);
        $this->assertEquals('test1', $result);
    }

    public function testWithStringThatContainsEmptyTag(): void
    {
        $result = $this->shuffleFunction->execute([',', 'test1' . $this->tagReplacer->getEmptyTag(), 'test2'], [',', 'test1' . $this->tagReplacer->getEmptyTag(), 'test2']);
        $this->assertEquals('test2', $result);
    }
}
