<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ShuffleFunction
 * Parser for 'shuffle' function :  returns the parameters shuffled.
 * The first parameter is the separator between each others.
 * Examples :
 * shuffle{ |one|two|three}
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
     * @param TagReplacerInterface $tr
     */
    public function __construct(TagReplacerInterface $tr)
    {
        $this->tagReplacer = $tr;
    }

    /**
     * Handle Shuffle function
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments)
    {
        if (count($arguments) < 2) {
            Throw new \InvalidArgumentException(
                sprintf("ShuffleParser expect at least two parameters, %d given.", count($arguments))
            );
        }

        $separator = array_shift($arguments);

        $arguments = array_map("trim", $arguments);

        shuffle($arguments);

        return implode($separator, $arguments);
    }

}