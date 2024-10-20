<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
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
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle set function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) !== 2) {
            throw new InvalidArgumentException(
                sprintf("SetFunction expect exactly two parameters (tag name and tag value), %d given.", count($arguments))
            );
        }

        $this->tagReplacer->addTag(mb_substr(trim((string) $originalArguments[0]), 1), $arguments[1]);

        return '';
    }

}
