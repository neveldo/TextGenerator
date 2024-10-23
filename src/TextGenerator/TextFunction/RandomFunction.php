<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacer;
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
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle Random function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, fn ($item): bool => mb_strpos((string) $item, $this->tagReplacer->getEmptyTag()) === false);

        if ($arguments === []) {
            return TagReplacer::EMPTY_TAG;
        }

        return $arguments[array_rand($arguments)];
    }

}
