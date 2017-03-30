<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class c
 * 'rmna' function : return the argument only if it does not contain any empty values
 * Examples :
 * #rmna{one @possible_not_available_tag two}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class RmnaFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * RandomFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle rmna function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments)
    {
        if (count($arguments) === 0
            || mb_strpos($arguments[0], $this->tagReplacer->getEmptyTag()) !== false
        ) {
            return '';
        }

        return $arguments[0];
    }

}