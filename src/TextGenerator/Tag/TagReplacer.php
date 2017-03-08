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
     * @const string the empty tag
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
     * @var array reversed tags to use for text replacement
     * format : ['@tag_name' => 'tag_name', ...]
     */
    private $sanitizedTagNames = [];

    /**
     * Initialize the tags list
     * @param array $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags)
    {
        $this->tags = $this->escapedTags = [];

        foreach($tags as $name => $value) {
            $this->addTag($name, $value);
        }
    }

    /**
     * Add a tag to the collection
     * @param string $name the tag name
     * @param string $value the tag value
     */
    public function addTag($name, $value)
    {
        $this->tags[$name] = $value;
        $this->sanitizedTagNames['@' . $name] = $name;

        // Replace empty values by [EMPTY_TAG] in order to be able to remove easily
        // the parts that contains this tag later to avoid inconsistent sentences
        if ($value === null || $value === '') {
            $value = $this->getEmptyTag();
        }

        if (is_scalar($value)) {
            $this->escapedTags['@' . $name] = $value;
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
     * Replace tags by the matching sanitized tag names
     * @param string $content
     * @return string
     */
    public function sanitizeTagNames($content)
    {
        return strtr($content, $this->sanitizedTagNames);
    }

    /**
     * Return a tag by its name
     * @param $name ex : '@tag_name'
     * @return string|array
     */
    public function getTag($name)
    {
        $name = mb_substr($name, 1);
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