<?php

namespace Neveldo\TextGenerator\TextFunction;

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
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * ProbabilityRandomFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle prandom function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments)
    {
        if (count($arguments) < 1) {
            throw new \InvalidArgumentException(
                sprintf("ProbabilityRandomFunction expect at least one parameter, %d given.", count($arguments))
            );
        }

        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, function($item) {
            return  (mb_strpos($item, $this->tagReplacer->getEmptyTag()) === false);
        });

        if (count($arguments) === 0) {
            return TagReplacer::EMPTY_TAG;
        }

        $options = [];
        $probabilities = [];
        $optionId = 0;

        foreach($arguments as $argument) {

            if (mb_strpos($argument, ':') === false) {
                continue;
            }

            $probability = (int) mb_substr($argument, 0, mb_strpos($argument, ':'));

            if ($probability <= 0) {
                continue;
            }

            $value = '';
            if (mb_strpos($argument, ':') + 1 < mb_strlen($argument)) {
                $value = mb_substr($argument, mb_strpos($argument, ':') + 1);
            }

            $options[$optionId] = [
                'probability' => $probability,
                'value' => $value,
            ];

            $probabilities = array_merge($probabilities, array_fill(0, $probability, $optionId));
            ++$optionId;
        }

        if (count($options) === 0) {
            return TagReplacer::EMPTY_TAG;
        }

        return $options[$probabilities[array_rand($probabilities)]]['value'];
    }
}