<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\FilterFunction;

class FilterFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private FilterFunction $filterFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->filterFunction = new FilterFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filterFunction->execute([], []);
    }

    public function testWithOneArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filterFunction->execute(['round'], ['round']);
    }

    public function testRound(): void
    {
        $result = $this->filterFunction->execute(['round', '3.44444'], ['round', '3.44444']);
        $this->assertEquals(3, $result);
    }

    public function testRound2(): void
    {
        $result = $this->filterFunction->execute(['round', '3.44444', '1'], ['round', '3.44444', '1']);
        $this->assertEquals(3.4, $result);
    }

    public function testUnexistantFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filterFunction->execute(['unexistant', '3.44444', '1'], ['unexistant', '3.44444', '1']);
    }

    public function testTooManyParams(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filterFunction->execute(['round', '3.44444', '1', '3.44444', '1', '3.44444', '1', '3.44444', '1'], ['round', '3.44444', '1', '3.44444', '1', '3.44444', '1', '3.44444', '1']);
    }
}
