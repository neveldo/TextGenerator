<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class CoalesceFunction
 * 'coalesce' function : return the first non empty argument
 * @package Neveldo\TextGenerator\TextFunction
 */
class CoalesceFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle coalesce function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        foreach ($arguments as $argument) {
            if ($argument !== $this->tagReplacer->getEmptyTag()
                && $argument !== null
                && $argument !== ''
            ) {
                return $argument;
            }
        }

        return TagReplacer::EMPTY_TAG;
    }

}
