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
    public const EMPTY_TAG = '[EMPTY]';

    /**
     * @var array<string, string|array<int,array<string,string>>> tags, format : ['tag_name' => 'value', ...]
     */
    private array $tags = [];

    /**
     * @var array<string,string> escaped tags to use for text replacement
     * format : ['@tag_name' => 'value', ...]
     */
    private array $escapedTags = [];

    /**
     * @var array<string,string> reversed tags to use for text replacement
     * format : ['@tag_name' => 'tag_name', ...]
     */
    private array $sanitizedTagNames = [];

    /**
     * Initialize the tags list
     * @param array<string, string|array<int,array<string,string>>> $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags): void
    {
        $this->tags = $this->escapedTags = [];

        foreach ($tags as $name => $value) {
            $this->addTag($name, $value);
        }
    }

    /**
     * Add a tag to the collection
     * @param string|array<int,array<string,string>> $value
     */
    public function addTag(string $name, string|array $value): void
    {
        $this->tags[$name] = $value;
        $this->sanitizedTagNames['@' . $name] = $name;

        // Replace empty values by [EMPTY_TAG] in order to be able to remove easily
        // the parts that contains this tag later to avoid inconsistent sentences
        if ($value === '') {
            $value = $this->getEmptyTag();
        }

        if (is_string($value)) {
            $this->escapedTags['@' . $name] = $value;
        }
    }

    /**
     * Replace tags by the matching values within the content
     */
    public function replace(string $content): string
    {
        return strtr($content, $this->escapedTags);
    }

    /**
     * Replace tags by the matching sanitized tag names
     */
    public function sanitizeTagNames(string $content): string
    {
        return strtr($content, $this->sanitizedTagNames);
    }

    /**
     * Return a tag by its name. ex : '@tag_name'
     * @return string|array<int,array<string,string>>|null
     */
    public function getTag(string $name): string|array|null
    {
        $name = mb_substr((string) $name, 1);
        return $this->tags[$name] ?? null;
    }

    /**
     * Return the array of available tags
     * @return array<string,string|array<int,array<string,string>>>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Return the array of escaped tags
     * @return array<string, string>
     */
    public function getEscapedTags(): array
    {
        return $this->escapedTags;
    }

    /**
     * @return string the empty tag
     */
    public function getEmptyTag(): string
    {
        return self::EMPTY_TAG;
    }
}
