<?php

namespace Neveldo\TextGenerator;

use PHPUnit\Framework\TestCase;

class SetFunctionTest extends TestCase
{
    private TextGenerator $textGenerator;

    public function setUp(): void
    {
        $this->textGenerator = new TextGenerator();
    }

    public function testSimpleAssignement(): void
    {
        $this->textGenerator->compile("#set{@test|test 123.}@test");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('test 123.', $result);
    }

    public function testAssignementWithAnotherTag(): void
    {
        $this->textGenerator->compile("#set{@test2|@test1 ipsum}@test2");
        $result = $this->textGenerator->generate(['test1' => 'Lorem']);
        $this->assertEquals('Lorem ipsum', $result);
    }

    public function testAssignementWithAnotherTag2(): void
    {
        $this->textGenerator->compile("#set{@test1|Lorem}#set{@test2|@test1 ipsum}@test2");

        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Lorem ipsum', $result);
    }

    public function testTagOverwritting(): void
    {
        $this->textGenerator->compile("#set{@test|Lorem2}@test");
        $result = $this->textGenerator->generate(['test' => 'Lorem1']);
        $this->assertEquals('Lorem2', $result);
    }

    public function testTagOverwritting2(): void
    {
        $this->textGenerator->compile("#set{@test|Lorem2}#set{@test|Lorem3}@test");
        $result = $this->textGenerator->generate([]);
        $this->assertEquals('Lorem3', $result);
    }

    public function testAssignementWithFunctionCall(): void
    {
        $this->textGenerator->compile("#set{@test|#if{sex == 'm'|he|she}}@test");
        $result = $this->textGenerator->generate(['sex' => 'f']);
        $this->assertEquals('she', $result);
    }
}
