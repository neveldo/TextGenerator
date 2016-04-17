<?php

namespace Neveldo\TextGenerator\Tag;

/**
 * Interface TagReplacerInterface
 * Interface for services that handle tags replacement within a text
 * @package Neveldo\TextGenerator\Tag
 */
interface TagReplacerInterface
{
    /**
     * Initialize the tags list
     * @param array $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags);

    /**
     * Replace tags by the matching values within the content
     * @param string $content
     * @return string
     */
    public function replace($content);

    /**
     * Return a tag by its name
     * @param $name
     * @return string|array
     */
    public function getTag($name);

    /**
     * Return the array of available tags
     * @return array
     */
    public function getTags();

    /**
     * Return the array of escaped tags
     * @return array
     */
    public function getEscapedTags();

    /**
     * @return string the empty tag
     */
    public function getEmptyTag();
}