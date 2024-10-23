<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
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
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle Shuffle function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) < 2) {
            throw new InvalidArgumentException(
                sprintf("ShuffleFunction expect at least two parameters, %d given.", count($arguments))
            );
        }

        $separator = array_shift($arguments);

        $arguments = array_map("trim", $arguments);

        // Remove empty arguments and arguments that contain empty tags
        $arguments = array_filter($arguments, fn ($item): bool => $item !== '' && mb_strpos($item, $this->tagReplacer->getEmptyTag()) === false);

        shuffle($arguments);

        return implode($separator, $arguments);
    }

}
