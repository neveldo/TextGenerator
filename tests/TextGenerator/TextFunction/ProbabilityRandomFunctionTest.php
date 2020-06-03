<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\ProbabilityRandomFunction;

class ProbabilityRandomFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new ProbabilityRandomFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute([], []);
    }

    public function testWithOneArgument()
    {
        $result = $this->function->execute(['1:test'], ['1:test']);
        $this->assertEquals('test', $result);
    }

    public function testWithTwoArguments()
    {
        $result = $this->function->execute(['2:test1', '8:test2'], ['2:test1', '8:test2']);
        $this->assertContains($result, ['test1', 'test2']);
    }

    public function testWithStringThatContainsEmptyTag()
    {
        $result = $this->function->execute(['9:test1' . $this->tagReplacer->getEmptyTag(), '1:test2'], ['9:test1' . $this->tagReplacer->getEmptyTag(), '1:test2']);
        $this->assertEquals('test2', $result);
    }

    public function testWithStringThatContainsEmptyTag2()
    {
        $result = $this->function->execute(['9:test1' . $this->tagReplacer->getEmptyTag()], ['9:test1' . $this->tagReplacer->getEmptyTag()]);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities()
    {
        $result = $this->function->execute(['2:test1', 'test2'], ['2:test1', 'test2']);
        $this->assertContains($result, ['test1']);
    }

    public function testWithWrongProbabilities2()
    {
        $result = $this->function->execute(['test1', 'test2'], ['test1', 'test2']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities3()
    {
        $result = $this->function->execute(['xx:test1', 'yy:test2'], ['xx:test1', 'yy:test2']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithWrongProbabilities4()
    {
        $result = $this->function->execute(['-5:test1', '-8:test2'], ['-5:test1', '-8:test2']);
        $this->assertEquals('[EMPTY]', $result);
    }
}