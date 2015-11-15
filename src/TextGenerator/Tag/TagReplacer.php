<?php

namespace Neveldo\TextGenerator\Tag;

/**
 * Class TagReplacer
 * Replace tags wihin a string
 * @package Neveldo\TextGenerator\Tag
 */
class TagReplacer implements TagReplacerInterface
{
    private $tags;

    /**
     * Initialize the tags list
     * @param array $tags, format : ['[tag_name]' => 'value', ...]
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * Replace tags by the matching values within the content
     * @param string $content
     * @return string
     */
    public function replace($content)
    {
        return strtr($content, $this->tags);
    }
}