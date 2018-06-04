<?php

namespace Neveldo\TextGenerator\Tag;

class TagReplacerTest extends \PHPUnit\Framework\TestCase
{
    public function testSetTags()
    {
        $tr = new TagReplacer();
        $tr->setTags(['tag1' => 'val1', 'tag2' => 'val2']);
        $this->assertEquals(['tag1' => 'val1', 'tag2' => 'val2'], $tr->getTags());
        $this->assertEquals(['@tag1' => 'val1', '@tag2' => 'val2'], $tr->getEscapedTags());
    }

    public function testSetTagsWithArrayTag()
    {
        $tr = new TagReplacer();
        $tr->setTags(['tag1' => 'val1', 'tag2' => ['sub_tag' => 'sub_val']]);
        $this->assertEquals(['tag1' => 'val1', 'tag2' => ['sub_tag' => 'sub_val']], $tr->getTags());
        $this->assertEquals(['@tag1' => 'val1'], $tr->getEscapedTags());
    }

    public function testSetTagsWithEmptyTag()
    {
        $tr = new TagReplacer();
        $tr->setTags(['tag1' => '', 'tag2' => 'val1', 'tag3' => null]);
        $this->assertEquals(['tag1' => '', 'tag2' => 'val1', 'tag3' => null], $tr->getTags());
        $this->assertEquals(['@tag1' => $tr->getEmptyTag(), '@tag2' => 'val1', '@tag3' => $tr->getEmptyTag()], $tr->getEscapedTags());
    }

    public function testReplaceRegularTags()
    {
        $tr = new TagReplacer();
        $tr->setTags(['tag1' => 'val1', 'tag2' => 'val2']);
        $result = $tr->replace("test1@tag1test2 @tag2 test3 @tag3 tag2 test4");
        $this->assertEquals("test1val1test2 val2 test3 @tag3 tag2 test4", $result);
    }

    public function testReplaceTagsWithEmptyTag()
    {
        $tr = new TagReplacer();
        $tr->setTags(['tag1' => '', 'tag2' => 'val1', 'tag3' => null]);

        $result = $tr->replace("test @tag1 test @tag2 test @tag3");
        $this->assertEquals("test " . $tr->getEmptyTag() . " test val1 test " . $tr->getEmptyTag(), $result);
    }
}