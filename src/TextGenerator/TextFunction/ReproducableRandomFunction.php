<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ReproducableRandomFunction
 * reproducable 'random' function :  returns the same random choice of one of the function arguments
 * Examples :
 * #random{seed|one|two|three}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ReproducableRandomFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * ReprodudableRandomFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle Reproducable Random function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments)
    {
        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, function($item) {
            return  (mb_strpos($item, $this->tagReplacer->getEmptyTag()) === false);
        });

        if (count($arguments) === 0) {
            return TagReplacer::EMPTY_TAG;
        }
        
        $seed = array_shift($arguments);
        $twister = new \mersenne_twister\twister(md5($seed));
        $key = $twister->rangeint(0, sizeof($arguments) - 1);

        return $arguments[$key];
    }

}