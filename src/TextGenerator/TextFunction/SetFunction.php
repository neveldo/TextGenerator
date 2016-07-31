<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class SetFunction
 * 'set' function : allows to add new misc tags to the tag replacer
 * directly from the template
 * Examples :
 * #set{my_tag|my value}
 * @package Neveldo\TextGenerator\TextFunction
 */
class SetFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * SetFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle set function
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments)
    {
        if (count($arguments) !== 2) {
            throw new \InvalidArgumentException(
                sprintf("SetFunction expect exactly two parameters (tag name and tag value), %d given.", count($arguments))
            );
        }

        $this->tagReplacer->addTag($arguments[0], $arguments[1]);

        return '';
    }

}