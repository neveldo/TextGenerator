<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ChooseFunction
 * 'choose' function :  returns one item from the function arguments
 * Examples :
 * #random{2|one|two|three} will output 'two'
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ChooseFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * ChooseFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle choose function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments)
    {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException(
                sprintf("ChooseFunction expect at least two parameters, %d given.", count($arguments))
            );
        }

        $index = (int) $arguments[0];

        if ($index !== 0
            && isset($arguments[$index])
            && (strpos($arguments[$index], $this->tagReplacer->getEmptyTag()) === false)
        ) {
            return $arguments[$index];
        }

        return '';
    }

}