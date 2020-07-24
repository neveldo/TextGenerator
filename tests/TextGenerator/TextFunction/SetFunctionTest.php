<?php

namespace Neveldo\TextGenerator;

class SetFunctionTest extends \PHPUnit\Framework\TestCase
{
    public function setup(): void {
        $this->textGenerator = new TextGenerator();
    }

    public function testSimpleAssignement()
    {
        $this->textGenerator->compile("#set{@test|test 123.}@test");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('test 123.', $result);
    }

    public function testAssignementWithAnotherTag()
    {
        $this->textGenerator->compile("#set{@test2|@test1 ipsum}@test2");
        $result = $this->textGenerator->generate(['test1' => 'Lorem']);
        $this->assertEquals('Lorem ipsum', $result);
    }

    public function testAssignementWithAnotherTag2()
    {
        $this->textGenerator->compile("#set{@test1|Lorem}#set{@test2|@test1 ipsum}@test2");

        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Lorem ipsum', $result);
    }

    public function testTagOverwritting()
    {
        $this->textGenerator->compile("#set{@test|Lorem2}@test");
        $result = $this->textGenerator->generate(['test' => 'Lorem1']);
        $this->assertEquals('Lorem2', $result);
    }

    public function testTagOverwritting2()
    {
        $this->textGenerator->compile("#set{@test|Lorem2}#set{@test|Lorem3}@test");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Lorem3', $result);
    }

    public function testAssignementWithFunctionCall()
    {
        $this->textGenerator->compile("#set{@test|#if{sex == 'm'|he|she}}@test");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals('she', $result);
    }
}
