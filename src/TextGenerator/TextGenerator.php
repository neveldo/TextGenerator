<?php

namespace Neveldo\TextGenerator;

use Neveldo\TextGenerator\TextFunction\ChooseFunction;
use Neveldo\TextGenerator\TextFunction\CoalesceFunction;
use Neveldo\TextGenerator\TextFunction\ExprFunction;
use Neveldo\TextGenerator\TextFunction\FilterFunction;
use Neveldo\TextGenerator\TextFunction\FunctionInterface;
use Neveldo\TextGenerator\TextFunction\IfFunction;
use Neveldo\TextGenerator\TextFunction\LoopFunction;
use Neveldo\TextGenerator\TextFunction\ProbabilityRandomFunction;
use Neveldo\TextGenerator\TextFunction\RandomFunction;
use Neveldo\TextGenerator\TextFunction\RmnaFunction;
use Neveldo\TextGenerator\TextFunction\SetFunction;
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
    private $statementsStack = [];

    /**
     * @var array sorted execution stack to run for generating a text
     */
    private $sortedStatementsStack = [];


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
            ->registerFunction('set', new SetFunction($this->tagReplacer))
            ->registerFunction('prandom', new ProbabilityRandomFunction($this->tagReplacer))
            ->registerFunction('choose', new ChooseFunction($this->tagReplacer))
            ->registerFunction('expr', new ExprFunction($this->tagReplacer))
            ->registerFunction('filter', new FilterFunction($this->tagReplacer))
            ->registerFunction('coalesce', new CoalesceFunction($this->tagReplacer))
            ->registerFunction('rmna', new RmnaFunction($this->tagReplacer))
        ;
    }

    /**
     * Generate an automated text from data
     * @param array $data that will feed the tags within the template
     * @return string the generated text
     * @throw Exception
     */
    public function generate(array $data)
    {
        if ($this->compiledTemplate === null) {
            return '';
        }

        $this->tagReplacer->setTags($data);

        $text = $this->compiledTemplate;

        // Execute the functions stack starting with the deepest functions and ending
        // with the shallowest ones
        foreach($this->sortedStatementsStack as $statement) {

            $openingTag = '[' . $statement['id'] . ']';
            $closingTag =  '[/' . $statement['id'] . ']';
            $openingTagLastPos = mb_strpos($text, $openingTag) + mb_strlen($openingTag);

            // Extract the argument list  of the function
            $arguments = mb_substr(
                $text,
                $openingTagLastPos,
                mb_strpos($text, $closingTag) - $openingTagLastPos
            );
            $parsedArguments = explode('|', $this->tagReplacer->replace($arguments));
            $originalArguments = explode('|', $arguments);

            // Replace the function call in the template by the returned value
            $text = $this->substringReplace(
                $text,
                $this->getFunction($statement['function'])->execute($parsedArguments, $originalArguments),
                mb_strpos($text, $openingTag),
                mb_strpos($text, $closingTag) + mb_strlen($closingTag) - mb_strpos($text, $openingTag)
            );
        }

        // Replace the remaining tags by the proper values
        $text = $this->tagReplacer->replace($text);

        // Remove trailing 'empty' tags
        $text = str_replace($this->tagReplacer->getEmptyTag(), '', $text);

        return $text;
    }

    /**
     * substr_replace that works with multibytes strings
     * @see http://php.net/manual/en/function.substr-replace.php
     * @param $str
     * @param $replacement
     * @param $start
     * @param $length
     * @return string
     */
    protected function substringReplace($str, $replacement, $start, $length)
    {
        return mb_substr($str, 0, $start) . $replacement . mb_substr($str, $start + $length);
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
        $this->statementsStack = [];
        $this->sortedStatementsStack = [];
        $this->executionStackSize = 0;

        $this->compiledTemplate = $this->parseIndentations($template);
        $this->compiledTemplate = $this->compileTemplate($this->compiledTemplate);
        $this->sortStatements();

        return $this;
    }

    /**
     * Sort the function calls tree from left to right and from bottom to up
     * @param $parent null|int parent statement ID
     */
    public function sortStatements($parent = null)
    {
        foreach($this->statementsStack as $id => $options) {
            if ($options['parent'] === $parent) {
                $this->sortStatements($id);
            }
        }

        if ($parent !== null) {
            $this->sortedStatementsStack[] = $this->statementsStack[$parent];
        }
    }

    /**
     * Remove the ;; followed by any space characters from the
     * template
     * @param string $template
     * @return string
     */
    public function parseIndentations($template)
    {
        return preg_replace('/;;\s+/m', '', $template);
    }

    /**
     * Parse recursively the template to extract the execution stack
     * @param string $template
     * @param int|null $parent parent function ID
     * @return string $template
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    protected function compileTemplate($template, $parent = null) {
        if (is_array($template)) {

            // $template = '#fct{...}'
            $template = $template[1];

            // Add the function call into the execution stack
            $functionName = mb_substr($template, 1, mb_strpos($template, '{') - 1);
            if (!in_array($functionName, array_keys($this->functions))) {
                throw new \InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $functionName));
            }
            $this->statementsStack[] = ['id' => $this->executionStackSize, 'function' => $functionName, 'parent' => $parent];

            // Update the template to replace function calls by references to the execution stack like : [9]...[/9]
            $template = $this->substringReplace($template, '[' . $this->executionStackSize . ']', 0, mb_strpos($template, '{') + 1);
            $template = $this->substringReplace($template, '[/' . $this->executionStackSize . ']', mb_strlen($template) - 1, 1);
            $parent = $this->executionStackSize;

            ++$this->executionStackSize;
        }

        return preg_replace_callback(
            '/(#[a-z_]+\{(?:[^\{\}]|(?R))+\})/us',
            function($template) use($parent) {
                return $this->compileTemplate($template, $parent);
            },
            $template
        );
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
