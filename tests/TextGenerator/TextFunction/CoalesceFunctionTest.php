<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\CoalesceFunction;

class CoalesceFunctionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new CoalesceFunction($this->tagReplacer);
    }

    public function testWithZeroArgument()
    {
        $result = $this->function->execute([], []);
        $this->assertEquals('', $result);
    }

    public function testWithOneEmptyArgument()
    {
        $result = $this->function->execute([''], ['']);
        $this->assertEquals('', $result);
    }

    public function testWithOneNullArgument()
    {
        $result = $this->function->execute([null], [null]);
        $this->assertEquals('', $result);
    }

    public function testWithOneRegularArgument()
    {
        $result = $this->function->execute(['Hello'], ['Hello']);
        $this->assertEquals('Hello', $result);
    }

    public function testEmptyAndRegularsArguments()
    {
        $result = $this->function->execute(
            [$this->tagReplacer->getEmptyTag(), null, '', 'value1', 'value2'],
            [$this->tagReplacer->getEmptyTag(), null, '', 'value1', 'value2']
        );
        $this->assertEquals('value1', $result);
    }
}