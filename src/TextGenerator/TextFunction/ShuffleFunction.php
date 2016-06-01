<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ShuffleFunction
 * 'shuffle' function :  returns the parameters shuffled.
 * The first parameter is the separator between each others.
 * Examples :
 * #shuffle{ |one|two|three}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ShuffleFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * ShuffleParser constructors
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle Shuffle function
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments)
    {
        if (count($arguments) < 2) {
            Throw new \InvalidArgumentException(
                sprintf("ShuffleFunction expect at least two parameters, %d given.", count($arguments))
            );
        }

        $separator = array_shift($arguments);

        $arguments = array_map("trim", $arguments);

        // Remove empty arguments and arguments that contain empty tags
        $arguments = array_filter($arguments, function($item) {
           return  ($item !== '' && strpos($item, $this->tagReplacer->getEmptyTag()) === false);
        });

        shuffle($arguments);

        return implode($separator, $arguments);
    }

}