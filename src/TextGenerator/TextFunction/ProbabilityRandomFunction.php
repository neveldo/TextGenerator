<?php

namespace Neveldo\TextGenerator\TextFunction;

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
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments)
    {
        // Remove arguments that contain empty tags
        $arguments = array_filter($arguments, function($item) {
            return  (strpos($item, $this->tagReplacer->getEmptyTag()) === false);
        });

        if (count($arguments) === 0) {
            return '';
        }

        $options = [];
        $probabilities = [];
        $optionId = 0;

        foreach($arguments as $argument) {

            if (strpos($argument, ':') === false) {
                continue;
            }

            $probability = (int) substr($argument, 0, strpos($argument, ':'));

            $value = '';
            if (strpos($argument, ':') + 1 < strlen($argument)) {
                $value = substr($argument, strpos($argument, ':') + 1);
            }

            $options[$optionId] = [
                'probability' => $probability,
                'value' => $value,
            ];

            $probabilities = array_merge($probabilities, array_fill(0, $probability, $optionId));
            ++$optionId;
        }

        if (count($options) === 0) {
            return '';
        }

        return $options[$probabilities[array_rand($probabilities)]]['value'];
    }
}