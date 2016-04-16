<?php

namespace Neveldo\TextGenerator;

use Neveldo\TextGenerator\TextFunction\IfFunction;
use Neveldo\TextGenerator\TextFunction\FunctionInterface;
use Neveldo\TextGenerator\TextFunction\RandomFunction;
use Neveldo\TextGenerator\TextFunction\ShuffleFunction;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class TextGenerator
 * Allow to generate automated texts from a template and data
 * @package Neveldo\TextGenerator
 */
class TextGenerator
{
    /**
     * @var FunctionInterface[] collection of function
     */
    private $functions = [];

    /**
     * @var TagReplacerInterface
     */
    private $tagReplacer;

    /**
     * @var string the template to handle
     */
    private $template;

    /**
     * TextGenerator constructor.
     * @param TagReplacerInterface|null $tr
     */
    public function __construct(TagReplacerInterface $tr = null)
    {
        // Init the tag replacer
        if ($tr === null) {
            $tr = new TagReplacer();
        }
        $this->tagReplacer = $tr;

        // Init core text functions
        $this
            ->registerFunction('shuffle', new ShuffleFunction($this->tagReplacer))
            ->registerFunction('random', new RandomFunction($this->tagReplacer))
            ->registerFunction('if', new IfFunction($this->tagReplacer))
        ;
    }

    /**
     * Generate an automated text from data
     * @param array $data data for filling the template
     * @return string the generated text
     */
    public function generate(array $data)
    {
        if ($this->template === null) {
            return '';
        }

        $this->tagReplacer->setTags($data);

        return $this->tagReplacer->replace(
            $this->parse($this->template)
        );
    }

    /**
     * Parse recursively the input for function calls
     * @param $input string|array the input to parse
     * @return string
     */
    private function parse($input)
    {
        if (is_array($input)) {

            // Match the function name and the argment lists
            preg_match('/#([a-z_]+)\{(.*)\}/s', $input[0], $matches);

            // Split the arguments list (separator = '|')
            $strArray = preg_split('//u', $matches[2], -1, PREG_SPLIT_NO_EMPTY);

            $depth = 0;
            $arguments = [];
            $currentArgument = '';
            $strSize = count($strArray);
            for ($i = 0; $i < $strSize; ++$i) {
                if (($strArray[$i] === '|') && $depth  === 0) {
                    $arguments[] = $currentArgument;
                    $currentArgument = '';
                    continue;
                }

                if ($strArray[$i] === '{') {
                    ++$depth;
                }

                if ($strArray[$i] === '}') {
                    --$depth;
                }

                $currentArgument .= $strArray[$i];

                if ($i === ($strSize - 1)) {
                    $arguments[] = $currentArgument;
                }
            }

            // Call the proper text function
            $input = $this->getFunction($matches[1])->execute($arguments);
        }
        return preg_replace_callback('/(#[a-z_]+\{(?:[^\{\}]|(?R))+\})/s', [$this, 'parse'], $input);
    }

    /**
     * Register a text function
     * @param $name function name to be used within the template
     * @param FunctionInterface $function The text function
     * @return $this
     */
    public function registerFunction($name, FunctionInterface $function)
    {
        $this->functions[$name] = $function;
        return $this;
    }

    /**
     * Get a function from its name
     * @param $name
     * @return FunctionInterface
     * @Thow \RuntimeException if the function doesn't exist
     */
    public function getFunction($name)
    {
        if (!array_key_exists($name, $this->functions)) {
            Throw new \RuntimeException(sprintf("Error : function '%s' doesn't exist.", $name));
        }
        return $this->functions[$name];
    }

    /**
     * Set the tag replacer
     * @param TagReplacerInterface $tr
     * @return $this
     */
    public function setTagReplacer(TagReplacerInterface $tr)
    {
        $this->tagReplacer = $tr;
        return $this;
    }

    /**
     * return the current tag replacer
     * @return TagReplacerInterface
     */
    public function getTagReplacer()
    {
        return $this->tagReplacer;
    }

    /**
     * Set the template
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (substr_count($template, '{') !== substr_count($template, '}')) {
            Throw new \RuntimeException("Template syntax error, please check functions brackets.");
        }
        $this->template = $template;
        return $this;
    }
}