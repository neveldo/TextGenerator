<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class RandomFunction
 * Parser for 'random' function :  returns randomly one of the function arguments
 * Examples :
 * random{one|two|three}
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
     * RandomParser constructor.
     * @param TagReplacerInterface $tr
     */
    public function __construct(TagReplacerInterface $tr)
    {
        $this->tagReplacer = $tr;
    }

    /**
     * Handle Random function
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments)
    {
        return trim($arguments[array_rand($arguments)]);
    }

}