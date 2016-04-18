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
     * @const string the tag
     */
    const EMPTY_TAG = '[EMPTY]';

    /**
     * @var array tags, format : ['tag_name' => 'value', ...]
     */
    private $tags = [];

    /**
     * @var array escaped tags to use for text replacement
     * format : ['@tag_name' => 'value', ...]
     */
    private $escapedTags = [];

    /**
     * Initialize the tags list
     * @param array $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        $this->escapedTags = [];
        foreach($this->tags as $tag => $value) {

            // Replace empty values by [EMPTY_TAG] in order to be able to remove easily
            // the parts that contains this tag later to avoid inconsistent sentences
            if ($value === null || $value === '') {
                $value = $this->getEmptyTag();
            }

            if (is_scalar($value)) {
                $this->escapedTags['@' . $tag] = $value;
            }
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
     * Return a tag by its name
     * @param $name
     * @return string|array
     */
    public function getTag($name)
    {
        if (isset($this->tags[$name])) {
            return $this->tags[$name];
        }
        return null;
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

    /**
     * @return string the empty tag
     */
    public function getEmptyTag() {
        return self::EMPTY_TAG;
    }
}