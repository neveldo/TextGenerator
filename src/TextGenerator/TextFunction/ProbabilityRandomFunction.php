<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class ProbabilityRandomFunction
 * 'prandom' function :  returns randomly one of the function arguments
 * depending on its probability value
 * Examples :
 * #random{10:one|45:two|45:three}
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ProbabilityRandomFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle prandom function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) < 1) {
            throw new InvalidArgumentException(
                sprintf("ProbabilityRandomFunction expect at least one parameter, %d given.", count($arguments))
            );
        }

        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, fn ($item): bool => mb_strpos((string) $item, $this->tagReplacer->getEmptyTag()) === false);

        if ($arguments === []) {
            return TagReplacer::EMPTY_TAG;
        }

        $options = [];
        $probabilities = [];
        $optionId = 0;

        foreach ($arguments as $argument) {

            if (mb_strpos((string) $argument, ':') === false) {
                continue;
            }

            $probability = (int) mb_substr((string) $argument, 0, mb_strpos((string) $argument, ':'));

            if ($probability <= 0) {
                continue;
            }

            $value = '';
            if (mb_strpos((string) $argument, ':') + 1 < mb_strlen((string) $argument)) {
                $value = mb_substr((string) $argument, mb_strpos((string) $argument, ':') + 1);
            }

            $options[$optionId] = [
                'probability' => $probability,
                'value' => $value,
            ];

            $probabilities = array_merge($probabilities, array_fill(0, $probability, $optionId));
            ++$optionId;
        }

        if ($options === []) {
            return TagReplacer::EMPTY_TAG;
        }

        return $options[$probabilities[array_rand($probabilities)]]['value'];
    }
}
