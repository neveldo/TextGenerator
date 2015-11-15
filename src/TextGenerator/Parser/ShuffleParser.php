<?php

namespace Neveldo\TextGenerator\Parser;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ShuffleParser
 * Parser for 'shuffle' function :  returns the parameters shuffled.
 * The first parameter is the separator between each others.
 * Examples :
 * #shuffle{ |one|two|three}
 *
 * @package Neveldo\TextGenerator\Parser
 */
class ShuffleParser implements ParserInterface
{
    /**
     * @var TagReplacerInterface
     */
    private $tagReplacer;

    /**
     * IfParser constructor.
     * @param TagReplacerInterface $tr
     */
    public function __construct($tr)
    {
        $this->tagReplacer = $tr;
    }

    /**
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments)
    {

        if (count($arguments) < 2) {
            Throw new \RuntimeException(
                sprintf("ShuffleParser expect at least two parameters, %d given.", count($arguments))
            );
        }

        $separator = array_shift($arguments);

        $arguments = array_map("trim", $arguments);

        shuffle($arguments);

        return implode($separator, $arguments);
    }

}