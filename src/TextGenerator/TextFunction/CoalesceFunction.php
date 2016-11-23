<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ChooseFunction
 * 'choose' function : returns one item from the function arguments
 * Examples :
 * #random{2|one|two|three} will output 'two'
 *
 * @package Neveldo\TextGenerator\TextFunction
 */

/**
 * Class CoalesceFunction
 * 'coalesce' function : return the first non empty argument
 * @package Neveldo\TextGenerator\TextFunction
 */
class CoalesceFunction implements FunctionInterface
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
     * Handle coalesce function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments)
    {
        foreach($arguments as $argument) {
            if ($argument !== $this->tagReplacer->getEmptyTag()
                && $argument !== null
                && $argument !== ''
            ) {
                return $argument;
            }
        }

        return '';
    }

}