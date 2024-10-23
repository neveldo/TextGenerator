<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class RmnaFunction
 * 'rmna' function : return the argument only if it does not contain any empty values
 * Examples :
 * #rmna{one @possible_not_available_tag two}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class RmnaFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle rmna function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if ($arguments === []
            || mb_strpos((string) $arguments[0], $this->tagReplacer->getEmptyTag()) !== false
        ) {
            return '';
        }

        return $arguments[0];
    }

}
