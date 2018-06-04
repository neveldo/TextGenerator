<?php

namespace Neveldo\TextGenerator\Tag;

use Neveldo\TextGenerator\TextFunction\LoopFunction;

class LoopFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setUp() {
        $this->tagReplacer = new TagReplacer();
        $this->function = new LoopFunction($this->tagReplacer);
        $this->oneElementTag = [
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ]
            ]
        ];
        $this->twoElementsTag = [
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => 'test22',
                ]
            ]
        ];
        $this->threeElementsTag = [
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => 'test22',
                ],
                [
                    'var1' => 'test31',
                    'var2' => 'test32',
                ]
            ]
        ];
        $this->fourElementsTag = [
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => 'test22',
                ],
                [
                    'var1' => 'test31',
                    'var2' => 'test32',
                ],
                [
                    'var1' => 'test41',
                    'var2' => 'test42',
                ]
            ]
        ];
    }

    public function testWith5Arguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute(['','','','',''], ['','','','','']);
    }

    public function testWith7Arguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->function->execute(['','','','','', '', ''], ['','','','','', '', '']);
    }

    public function testRegularLoopOnOneElement()
    {
        $this->tagReplacer->setTags($this->oneElementTag);
        $result = $this->function->execute(['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }

    public function testRegularLoopOnTwoElements()
    {
        $this->tagReplacer->setTags($this->twoElementsTag);
        $result = $this->function->execute(['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2 and test21 - test22', $result);
    }

    public function testRegularLoopOnThreeElements()
    {
        $this->tagReplacer->setTags($this->threeElementsTag);
        $result = $this->function->execute(['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22 and test31 - test32', $result);
    }

    public function testRegularLoopOnFourElementsMaxZero()
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->function->execute(['@loop_tag', 0, false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', 0, false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('', $result);
    }

    public function testRegularLoopOnFourElementsMaxThree()
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->function->execute(['@loop_tag', 3, false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', 3, false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22 and test31 - test32', $result);
    }

    public function testRegularLoopOnFourElementsMaxFive()
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->function->execute(['@loop_tag', 5, false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', 5, false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22, test31 - test32 and test41 - test42', $result);
    }

    public function testRandomLoopWithTwoElements()
    {
        $this->tagReplacer->setTags([
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => 'test22',
                ]
            ]
        ]);
        $result = $this->function->execute(['@loop_tag', '*', true, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', true, ', ', ' and ', '@var1 - @var2']);
        $this->assertContains($result, ['test21 - test22 and test1 - test2', 'test1 - test2 and test21 - test22']);
    }

    public function testLoopWithAScalar()
    {
        $this->tagReplacer->setTags([
            'loop_tag' => 'not_an_array'
        ]);
        $result = $this->function->execute(['@loop_tag', '*', true, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', true, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('', $result);
    }

    public function testRegularLoopWithATagThatContainsAnEmptyTag()
    {
        $this->tagReplacer->setTags([
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => 'test22 ' . $this->tagReplacer->getEmptyTag() . 'test',
                ]
            ]
        ]);
        $result = $this->function->execute(['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }

    public function testRegularLoopWithATagThatContainsAnEmptyValue()
    {
        $this->tagReplacer->setTags([
            'loop_tag' => [
                [
                    'var1' => 'test1',
                    'var2' => 'test2',
                ],
                [
                    'var1' => 'test21',
                    'var2' => '',
                ]
            ]
        ]);
        $result = $this->function->execute(['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', false, ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }
}