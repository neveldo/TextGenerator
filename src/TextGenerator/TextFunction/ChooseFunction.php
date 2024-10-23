<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ChooseFunction
 * 'choose' function :  returns one item from the function arguments
 * Examples :
 * #choose{2|one|two|three} will output 'two'
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ChooseFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle choose function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) < 2) {
            throw new InvalidArgumentException(
                sprintf("ChooseFunction expect at least two parameters, %d given.", count($arguments))
            );
        }

        $index = (int) $arguments[0];

        if ($index !== 0
            && isset($arguments[$index])
            && (mb_strpos((string) $arguments[$index], $this->tagReplacer->getEmptyTag()) === false)
        ) {
            return $arguments[$index];
        }

        return TagReplacer::EMPTY_TAG;
    }

}
