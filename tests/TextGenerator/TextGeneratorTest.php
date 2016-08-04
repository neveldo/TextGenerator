<?php

namespace Neveldo\TextGenerator;

class TextGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->textGenerator = new TextGenerator();
    }

    public function testTagsReplacement()
    {
        $this->textGenerator->compile("Hello @firstname @lastname.");
        $result = $this->textGenerator->generate([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);
        $this->assertEquals('Hello John Doe.', $result);
    }

    public function testTagsReplacementWithEmptyTag()
    {
        $this->textGenerator->compile("Hello @firstname @lastname.");
        $result = $this->textGenerator->generate([
            'firstname' => 'John',
            'lastname' => '',
        ]);
        $this->assertEquals('Hello John ' . $this->textGenerator->getTagReplacer()->getEmptyTag() . '.', $result);
    }

    public function testRandom()
    {
        $this->textGenerator->compile("#random{Throughout|During|All along}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['Throughout', 'During', 'All along']);
    }

    public function testRandom2()
    {
        $this->textGenerator->compile("#random{Throughout|}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['Throughout', '']);
    }

    public function testRandomWithEmptyArg()
    {
        $this->textGenerator->compile("#random{Throughout|test" . $this->textGenerator->getTagReplacer()->getEmptyTag() . "test}");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals($result, 'Throughout');
    }

    public function testShuffle()
    {
        $this->textGenerator->compile("#shuffle{, |test1|test2}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['test1, test2', 'test2, test1']);
    }

    public function testShuffleWithEmptyArg()
    {
        $this->textGenerator->compile("#shuffle{, |test1||test" . $this->textGenerator->getTagReplacer()->getEmptyTag()  . "test|}");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals($result, 'test1');
    }

    public function testIf()
    {
        $this->textGenerator->compile("#if{sex == 'm'|actor|actress}");
        $result = $this->textGenerator->generate(['sex' => 'm']);
        $this->assertEquals($result, 'actor');
    }

    public function testElse()
    {
        $this->textGenerator->compile("#if{sex == 'm'|actor|actress}");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals($result, 'actress');
    }

    public function testElseWithNoElse() {
        $this->textGenerator->compile("#if{sex == 'm'|actor}");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals($result, '');
    }

    public function testLoopWithThreeElements()
    {
        $this->textGenerator->compile("#loop{loop_tag|*|false|, | and |@var1 - @var2}");
        $result = $this->textGenerator->generate([
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
        ]);
        $this->assertEquals($result, 'test1 - test2, test21 - test22 and test31 - test32');
    }

    public function testRandomLoopWithTwoElements()
    {
        $this->textGenerator->compile("#loop{loop_tag|*|true|, | and |@var1 - @var2}");
        $result = $this->textGenerator->generate([
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
        $this->assertContains($result, ['test21 - test22 and test1 - test2', 'test1 - test2 and test21 - test22']);
    }

    public function testNestedFunctions()
    {
        $this->textGenerator->compile("#shuffle{, |one|#random{two|three}}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['one, two', 'one, three', 'two, one', 'three, one']);
    }

    public function testIdentation()
    {
        $this->textGenerator->compile(
"Test1 ;;    Test2 ;;
        	    Test3;;   
            Test4"
        );
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Test1 Test2 Test3Test4', $result);
    }
}
