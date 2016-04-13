<?php

namespace Neveldo\TextGenerator\Tag;

/**
 * Class TagReplacerInterface
 * Handle tags replacement within a text
 * @package Neveldo\TextGenerator\Tag
 */
class TagReplacer implements TagReplacerInterface
{
    /**
     * @var array tags, format : ['tag_name' => 'value', ...]
     */
    private $tags;

    /**
     * @var array escaped tags to use for text replacement
     * format : ['@tag_name' => 'value', ...]
     */
    private $escapedTags;

    /**
     * Initialize the tags list
     * @param array $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        $this->escapedTags = [];
        foreach($this->tags as $tag => $value) {
            $this->escapedTags['@' . $tag] = $value;
        }
    }

    /**
     * Replace tags by the matching values within the content
     * @param string $content
     * @return string
     */
    public function replace($content)
    {
        return strtr($content, $this->escapedTags);
    }

    /**
     * Return the array of available tags
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Return the array of escaped tags
     * @return array
     */
    public function getEscapedTags()
    {
        return $this->escapedTags;
    }
}