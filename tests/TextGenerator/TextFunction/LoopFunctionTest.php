<?php

namespace Neveldo\TextGenerator\Tag;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Neveldo\TextGenerator\TextFunction\LoopFunction;

class LoopFunctionTest extends TestCase
{
    private TagReplacer $tagReplacer;
    private LoopFunction $loopFunction;

    /** @var array<string,array<int,array<string,string>>> **/
    private array $oneElementTag;

    /** @var array<string,array<int,array<string,string>>> **/
    private array $twoElementsTag;

    /** @var array<string,array<int,array<string,string>>> **/
    private array $threeElementsTag;

    /** @var array<string,array<int,array<string,string>>> **/
    private array $fourElementsTag;

    public function setUp(): void
    {
        $this->tagReplacer = new TagReplacer();
        $this->loopFunction = new LoopFunction($this->tagReplacer);
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

    public function testWith5Arguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loopFunction->execute(['','','','',''], ['','','','','']);
    }

    public function testWith7Arguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loopFunction->execute(['','','','','', '', ''], ['','','','','', '', '']);
    }

    public function testRegularLoopOnOneElement(): void
    {
        $this->tagReplacer->setTags($this->oneElementTag);
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }

    public function testRegularLoopOnTwoElements(): void
    {
        $this->tagReplacer->setTags($this->twoElementsTag);
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2 and test21 - test22', $result);
    }

    public function testRegularLoopOnThreeElements(): void
    {
        $this->tagReplacer->setTags($this->threeElementsTag);
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22 and test31 - test32', $result);
    }

    public function testRegularLoopOnFourElementsMaxZero(): void
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->loopFunction->execute(['@loop_tag', '0', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '0', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('', $result);
    }

    public function testRegularLoopOnFourElementsMaxThree(): void
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->loopFunction->execute(['@loop_tag', '3', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '3', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22 and test31 - test32', $result);
    }

    public function testRegularLoopOnFourElementsMaxFive(): void
    {
        $this->tagReplacer->setTags($this->fourElementsTag);
        $result = $this->loopFunction->execute(['@loop_tag', '5', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '5', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2, test21 - test22, test31 - test32 and test41 - test42', $result);
    }

    public function testRandomLoopWithTwoElements(): void
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
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'true', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'true', ', ', ' and ', '@var1 - @var2']);
        $this->assertContains($result, ['test21 - test22 and test1 - test2', 'test1 - test2 and test21 - test22']);
    }

    public function testLoopWithAScalar(): void
    {
        $this->tagReplacer->setTags([
            'loop_tag' => 'not_an_array'
        ]);
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'true', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'true', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('[EMPTY]', $result);
    }

    public function testRegularLoopWithATagThatContainsAnEmptyTag(): void
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
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }

    public function testRegularLoopWithATagThatContainsAnEmptyValue(): void
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
        $result = $this->loopFunction->execute(['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2'], ['@loop_tag', '*', 'false', ', ', ' and ', '@var1 - @var2']);
        $this->assertEquals('test1 - test2', $result);
    }
}
