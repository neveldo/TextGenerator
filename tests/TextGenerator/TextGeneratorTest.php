<?php

namespace Neveldo\TextGenerator;

use PHPUnit\Framework\TestCase;

class TextGeneratorTest extends TestCase
{
    private TextGenerator $textGenerator;

    public function setUp(): void
    {
        $this->textGenerator = new TextGenerator();
    }

    public function testTagsReplacement(): void
    {
        $this->textGenerator->compile("Hello @firstname @lastname.");
        $result = $this->textGenerator->generate([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);
        $this->assertEquals('Hello John Doe.', $result);
    }

    public function testTagsReplacementWithEmptyTag(): void
    {
        $this->textGenerator->compile("Hello @firstname @lastname.");
        $result = $this->textGenerator->generate([
            'firstname' => 'John',
            'lastname' => '',
        ]);
        $this->assertEquals('Hello John .', $result);
    }

    public function testRandom(): void
    {
        $this->textGenerator->compile("#random{Throughout|During|All along}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['Throughout', 'During', 'All along']);
    }

    public function testRandom2(): void
    {
        $this->textGenerator->compile("#random{Throughout|}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['Throughout', '']);
    }

    public function testRandomWithEmptyArg(): void
    {
        $this->textGenerator->compile("#random{Throughout|test" . $this->textGenerator->getTagReplacer()->getEmptyTag() . "test}");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals($result, 'Throughout');
    }

    public function testShuffle(): void
    {
        $this->textGenerator->compile("#shuffle{, |test1|test2}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['test1, test2', 'test2, test1']);
    }

    public function testShuffleWithEmptyArg(): void
    {
        $this->textGenerator->compile("#shuffle{, |test1||test" . $this->textGenerator->getTagReplacer()->getEmptyTag()  . "test|}");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals($result, 'test1');
    }

    public function testIf(): void
    {
        $this->textGenerator->compile("#if{@sex == 'm'|actor|actress}");
        $result = $this->textGenerator->generate(['sex' => 'm']);
        $this->assertEquals($result, 'actor');
    }

    public function testElse(): void
    {
        $this->textGenerator->compile("#if{@sex == 'm'|actor|actress}");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals($result, 'actress');
    }

    public function testElseWithNoElse(): void
    {
        $this->textGenerator->compile("#if{@sex == 'm'|actor}");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals($result, '');
    }

    public function testLoopWithThreeElements(): void
    {
        $this->textGenerator->compile("#loop{@loop_tag|*|false|, | and |@var1 - @var2}");
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

    public function testRandomLoopWithTwoElements(): void
    {
        $this->textGenerator->compile("#loop{@loop_tag|*|true|, | and |@var1 - @var2}");
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

    public function testNestedFunctions(): void
    {
        $this->textGenerator->compile("#shuffle{, |one|#random{two|three}}");
        $result = $this->textGenerator->generate([]);
        $this->assertContains($result, ['one, two', 'one, three', 'two, one', 'three, one']);
    }

    public function testIdentation(): void
    {
        $this->textGenerator->compile(
"Test1 ;;    Test2 ;;
        	    Test3;;   
            Test4"
        );
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Test1 Test2 Test3Test4', $result);
    }

    public function testCoalesce(): void
    {
        $this->textGenerator->compile("#coalesce{@my_tag1|@my_tag2|@my_tag3|@my_tag4}");
        $result = $this->textGenerator->generate([
            'my_tag1' => '',
            'my_tag2' => '',
            'my_tag3' => 'Hello',
            'my_tag4' => 'Hi',
        ]);
        $this->assertEquals($result, 'Hello');
    }

    public function testImbricatedFunctions(): void
    {
        $this->textGenerator->compile("Test imbricated set/filter#set{@my_tag1|#filter{round|3.55|1}0}. Test imbricated if/filter : #if{@my_tag1 == 3.6|ok #filter{round|@my_tag1} #filter{round|@my_tag1|1}|notok}.");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals($result, 'Test imbricated set/filter. Test imbricated if/filter : ok 4 3.6.');
    }

}
