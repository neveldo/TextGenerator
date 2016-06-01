<?php

namespace Neveldo\TextGenerator;

use Neveldo\TextGenerator\TextFunction\FunctionInterface;
use Neveldo\TextGenerator\TextFunction\IfFunction;
use Neveldo\TextGenerator\TextFunction\LoopFunction;
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
     * @var string the compiled template
     */
    private $compiledTemplate;

    /**
     * @var array execution stack to run for generating a text
     */
    private $executionStack = [];

    /**
     * @var int execution stack size
     */
    private $executionStackSize = 0;

    /**
     * TextGenerator constructor.
     * @param TagReplacerInterface|null $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer = null)
    {
        // Init the tag replacer
        if ($tagReplacer === null) {
            $tagReplacer = new TagReplacer();
        }
        $this->tagReplacer = $tagReplacer;

        // Init core text functions
        $this
            ->registerFunction('shuffle', new ShuffleFunction($this->tagReplacer))
            ->registerFunction('random', new RandomFunction($this->tagReplacer))
            ->registerFunction('if', new IfFunction($this->tagReplacer))
            ->registerFunction('loop', new LoopFunction($this->tagReplacer))
        ;
    }

    /**
     * Generate an automated text from data
     * @param array $data that will feed the tags within the template
     * @return string the generated text
     */
    public function generate(array $data)
    {
        if ($this->compiledTemplate === null) {
            return '';
        }

        $this->tagReplacer->setTags($data);

        // Replace the tags by the proper values first
        $text = $this->tagReplacer->replace($this->compiledTemplate);

        // Execute the functions stack starting with the deepest functions and ending
        // with the shallowest ones
        foreach($this->executionStack as $functionId => $functionName) {

            $openingTag = '[' . $functionId . ']';
            $closingTag =  '[/' . $functionId . ']';
            $openingTagLastPos = strpos($text, $openingTag) + strlen($openingTag);

            // Extract the argument list  of the function
            $arguments = substr(
                $text,
                $openingTagLastPos,
                strpos($text, $closingTag) - $openingTagLastPos
            );
            $arguments = explode('|', $arguments);

            // Replace the function call in the template by the returned value
            $text = substr_replace(
                $text,
                $this->getFunction($functionName)->execute($arguments),
                strpos($text, $openingTag),
                strpos($text, $closingTag) + strlen($closingTag) - strpos($text, $openingTag)
            );
        }

        return $text;
    }

    /**
     * Prepare the template by parsing the function calls within it
     * @param string $template The template to compile
     * @return $this
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    public function compile($template)
    {
        $this->template = $template;
        $this->executionStack = [];
        $this->executionStackSize = 0;
        $this->compiledTemplate = $this->compileTemplate($template);

        // Reverse the execution stack in order to execute the deepest
        // functions first and end with the shallowest ones
        $this->executionStack = array_reverse($this->executionStack, true);

        return $this;
    }

    /**
     * Parse recursively the template to extract the execution stack
     * @param string $template
     * @return string $template
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    protected function compileTemplate($template) {
        if (is_array($template)) {

            // $template = '#fct{...}'
            $template = $template[1];

            // Add the function call into the execution stack
            $functionName = substr($template, 1, strpos($template, '{') - 1);
            if (!in_array($functionName, array_keys($this->functions))) {
                throw new \InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $functionName));
            }
            $this->executionStack[$this->executionStackSize] = $functionName;

            // Update the template to replace function calls by references to the execution stack like : [9]...[/9]
            $template = substr_replace($template, '[' . $this->executionStackSize . ']', 0, strpos($template, '{') + 1);
            $template = substr_replace($template, '[/' . $this->executionStackSize . ']', strlen($template) - 1);

            ++$this->executionStackSize;
        }
        return preg_replace_callback('/(#[a-z_]+\{(?:[^\{\}]|(?R))+\})/s', [$this, 'compileTemplate'], $template);
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
     * @throw \InvalidArgumentException if the function doesn't exist
     */
    public function getFunction($name)
    {
        if (!array_key_exists($name, $this->functions)) {
            throw new \InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $name));
        }
        return $this->functions[$name];
    }

    /**
     * Set the tag replacer
     * @param TagReplacerInterface $tagReplacer
     * @return $this
     */
    public function setTagReplacer(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
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
}