<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\ProbabilityRandomFunction;

class ProbabilityRandomFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private ProbabilityRandomFunction $probabilityRandomFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->probabilityRandomFunction = new ProbabilityRandomFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->probabilityRandomFunction->execute([], []);
    }

    public function testWithOneArgument(): void
    {
        $result = $this->probabilityRandomFunction->execute(['1:test'], ['1:test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoArguments(): void
    {
        $result = $this->probabilityRandomFunction->execute(['2:test1', '8:test2'], ['2:test1', '8:test2']);
        $this->assertContains($result, ['test1', 'test2']);
    }

    public function testWithStringThatContainsEmptyTag(): void
    {
        $result = $this->probabilityRandomFunction->execute(['9:test1' . $this->tagReplacer->getEmptyTag(), '1:test2'], ['9:test1' . $this->tagReplacer->getEmptyTag(), '1:test2']);
        $this->assertEquals('test2', $result);
    }

    public function testWithStringThatContainsEmptyTag2(): void
    {
        $result = $this->probabilityRandomFunction->execute(['9:test1' . $this->tagReplacer->getEmptyTag()], ['9:test1' . $this->tagReplacer->getEmptyTag()]);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities(): void
    {
        $result = $this->probabilityRandomFunction->execute(['2:test1', 'test2'], ['2:test1', 'test2']);
        $this->assertContains($result, ['test1']);
    }

    public function testWithWrongProbabilities2(): void
    {
        $result = $this->probabilityRandomFunction->execute(['test1', 'test2'], ['test1', 'test2']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities3(): void
    {
        $result = $this->probabilityRandomFunction->execute(['xx:test1', 'yy:test2'], ['xx:test1', 'yy:test2']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities4(): void
    {
        $result = $this->probabilityRandomFunction->execute(['-5:test1', '-8:test2'], ['-5:test1', '-8:test2']);
        $this->assertEquals('[EMPTY]', $result);
    }
}
