<?php

namespace Neveldo\TextGenerator;

use Neveldo\TextGenerator\TextFunction\ChooseFunction;
use Neveldo\TextGenerator\TextFunction\ExprFunction;
use Neveldo\TextGenerator\TextFunction\FilterFunction;
use Neveldo\TextGenerator\TextFunction\FunctionInterface;
use Neveldo\TextGenerator\TextFunction\IfFunction;
use Neveldo\TextGenerator\TextFunction\LoopFunction;
use Neveldo\TextGenerator\TextFunction\ProbabilityRandomFunction;
use Neveldo\TextGenerator\TextFunction\RandomFunction;
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

        // Replace the tags by the proper values first
        $text = $this->tagReplacer->replace($this->compiledTemplate);

        // Execute the functions stack starting with the deepest functions and ending
        // with the shallowest ones
        foreach($this->statementsStack as $statement) {

            $openingTag = '[' . $statement['id'] . ']';
            $closingTag =  '[/' . $statement['id'] . ']';
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
                $this->getFunction($statement['function'])->execute($arguments),
                strpos($text, $openingTag),
                strpos($text, $closingTag) + strlen($closingTag) - strpos($text, $openingTag)
            );

            if ($statement['function'] === 'set') {
                // After a tag affectation, parse this tag in all the text in order to replace
                // Them with the proper value
                $text = $this->tagReplacer->replaceOne($text, $arguments[0]);
            }
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
        $this->statementsStack = [];

        $this->compiledTemplate = $this->parseIndentations($template);
        $this->compiledTemplate = $this->compileTemplate($this->compiledTemplate);

        // Sort the execution stack in order to execute the deepest
        // functions first and end with the shallowest ones but preserving the order of
        // calls that have the same depth level
        uasort(
            $this->statementsStack,
            function($a, $b) {
                if ($b['depth'] - $a['depth'] !== 0) {
                    return $b['depth'] - $a['depth'];
                } else {
                    return $a['id'] - $b['id'];
                }
            }
        );

        return $this;
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
     * @return string $template
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    protected function compileTemplate($template) {

        $replacements = -1;
        $depth = 0;
        $statementsStackSize = 0;
        while($replacements !== 0) {
            $template = preg_replace_callback(
                '/(#[a-z_]+\{(?:[^\{\}]|(?R))+\})/s',
                function($template) use ($depth, &$statementsStackSize) {
                    // $template = '#fct{...}'
                    $template = $template[1];

                    // Add the function call into the execution stack
                    $functionName = substr($template, 1, strpos($template, '{') - 1);
                    if (!in_array($functionName, array_keys($this->functions))) {
                        throw new \InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $functionName));
                    }
                    $this->statementsStack[$statementsStackSize] = [
                        'id' => $statementsStackSize,
                        'function' => $functionName,
                        'depth' => $depth
                    ];

                    // Update the template to replace function calls by references to the execution stack like : [9]...[/9]
                    $template = substr_replace($template, '[' . $statementsStackSize . ']', 0, strpos($template, '{') + 1);
                    $template = substr_replace($template, '[/' . $statementsStackSize . ']', strlen($template) - 1);

                    ++$statementsStackSize;

                    return $template;
                },
                $template,
                -1,
                $replacements
            );

            ++$depth;
        }
        return $template;
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