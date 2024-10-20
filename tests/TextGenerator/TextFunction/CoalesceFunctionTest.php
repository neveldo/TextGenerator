<?php

namespace Neveldo\TextGenerator\Tag;

use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\CoalesceFunction;

class CoalesceFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private CoalesceFunction $coalesceFunction;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->coalesceFunction = new CoalesceFunction($this->tagReplacer);
    }

    public function testWithZeroArgument(): void
    {
        $result = $this->coalesceFunction->execute([], []);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithOneEmptyArgument(): void
    {
        $result = $this->coalesceFunction->execute([''], ['']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testWithOneRegularArgument(): void
    {
        $result = $this->coalesceFunction->execute(['Hello'], ['Hello']);
        $this->assertEquals('Hello', $result);
    }

    public function testEmptyAndRegularsArguments(): void
    {
        $result = $this->coalesceFunction->execute(
            [$this->tagReplacer->getEmptyTag(), '', 'value1', 'value2'],
            [$this->tagReplacer->getEmptyTag(), '', 'value1', 'value2']
        );
        $this->assertEquals('value1', $result);
    }
}
