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
     * @param array<string, string|array<int,array<string,string>>> $tags, format : ['tag_name' => 'value', ...]
     */
    public function setTags(array $tags): void;

    /**
     * Add a tag to the collection
     * @param string|array<int,array<string,string>> $value
     */
    public function addTag(string $name, string|array $value): void;

    /**
     * Replace tags by the matching values within the content
     */
    public function replace(string $content): string;

    /**
     * Replace tags by the matching sanitized tag names
     */
    public function sanitizeTagNames(string $content): string;

    /**
     * Return a tag by its name. ex : '@tag_name'
     * @return string|array<int,array<string,string>>|null
     */
    public function getTag(string $name): string|array|null;

    /**
     * Return the array of available tags
     * @return array<string,string|array<int,array<string,string>>>
     */
    public function getTags(): array;

    /**
     * Return the array of escaped tags
     * @return array<string, string>
     */
    public function getEscapedTags(): array;

    /**
     * @return string the empty tag
     */
    public function getEmptyTag(): string;
}
