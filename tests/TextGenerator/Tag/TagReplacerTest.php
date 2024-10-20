<?php

namespace Neveldo\TextGenerator\Tag;

use PHPUnit\Framework\TestCase;

class TagReplacerTest extends TestCase
{
    public function testSetTags(): void
    {
        $tagReplacer = new TagReplacer();
        $tagReplacer->setTags(['tag1' => 'val1', 'tag2' => 'val2']);
        $this->assertEquals(['tag1' => 'val1', 'tag2' => 'val2'], $tagReplacer->getTags());
        $this->assertEquals(['@tag1' => 'val1', '@tag2' => 'val2'], $tagReplacer->getEscapedTags());
    }

    public function testSetTagsWithArrayTag(): void
    {
        $tagReplacer = new TagReplacer();
        $tagReplacer->setTags(['tag1' => 'val1', 'tag2' => [['sub_tag' => 'sub_val']]]);
        $this->assertEquals(['tag1' => 'val1', 'tag2' => [['sub_tag' => 'sub_val']]], $tagReplacer->getTags());
        $this->assertEquals(['@tag1' => 'val1'], $tagReplacer->getEscapedTags());
    }

    public function testSetTagsWithEmptyTag(): void
    {
        $tagReplacer = new TagReplacer();
        $tagReplacer->setTags(['tag1' => '', 'tag2' => 'val1', 'tag3' => '']);
        $this->assertEquals(['tag1' => '', 'tag2' => 'val1', 'tag3' => ''], $tagReplacer->getTags());
        $this->assertEquals(['@tag1' => $tagReplacer->getEmptyTag(), '@tag2' => 'val1', '@tag3' => $tagReplacer->getEmptyTag()], $tagReplacer->getEscapedTags());
    }

    public function testReplaceRegularTags(): void
    {
        $tagReplacer = new TagReplacer();
        $tagReplacer->setTags(['tag1' => 'val1', 'tag2' => 'val2']);
        $result = $tagReplacer->replace("test1@tag1test2 @tag2 test3 @tag3 tag2 test4");
        $this->assertEquals("test1val1test2 val2 test3 @tag3 tag2 test4", $result);
    }

    public function testReplaceTagsWithEmptyTag(): void
    {
        $tagReplacer = new TagReplacer();
        $tagReplacer->setTags(['tag1' => '', 'tag2' => 'val1', 'tag3' => '']);

        $result = $tagReplacer->replace("test @tag1 test @tag2 test @tag3");
        $this->assertEquals("test " . $tagReplacer->getEmptyTag() . " test val1 test " . $tagReplacer->getEmptyTag(), $result);
    }
}
