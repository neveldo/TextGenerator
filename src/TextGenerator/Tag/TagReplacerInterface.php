<?php

namespace Neveldo\TextGenerator\Tag;

/**
 * Interface TagReplacerInterface
 * @package Neveldo\TextGenerator\Tag
 */
interface TagReplacerInterface
{
    /**
     * Initialize the tags list
     * @param array $tags, format : ['[tag_name]' => 'value', ...]
     */
    public function setTags(array $tags);

    /**
     * Replace tags by the matching values within the content
     * @param string $content
     * @return string
     */
    public function replace($content);
}