<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class RandomFunction
 * 'random' function :  returns randomly one of the function arguments
 * Examples :
 * #random{one|two|three}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class RandomFunction implements FunctionInterface
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
     * Handle Random function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments)
    {
        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, function($item) {
            return  (strpos($item, $this->tagReplacer->getEmptyTag()) === false);
        });

        if (count($arguments) === 0) {
            return '';
        }

        return $arguments[array_rand($arguments)];
    }

}