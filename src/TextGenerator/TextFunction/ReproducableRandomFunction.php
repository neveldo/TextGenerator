<?php

namespace Neveldo\TextGenerator\TextFunction;

use mersenne_twister\twister;
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
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle Reproducable Random function
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

        $seed = array_shift($arguments);
        $twister = new twister(md5((string) $seed));
        $key = $twister->rangeint(0, count($arguments) - 1);

        return $arguments[$key];
    }

}
