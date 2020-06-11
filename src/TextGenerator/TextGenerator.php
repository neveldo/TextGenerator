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
use Neveldo\TextGenerator\TextFunction\ReproducableRandomFunction;	
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
     * @var array sorted execution stack to run for generating a text
     */
    private $executionStack = [];

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
            ->registerFunction('reprandom', new ReproducableRandomFunction($this->tagReplacer))
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
        foreach($this->executionStack as $statement) {

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

        $template = $this->parseIndentations($template);

        $data = $this->compileTemplate($template);

        $this->executionStack = $this->getSortedStatements($data['executionStack']);
        $this->compiledTemplate = $data['compiledTemplate'];

        return $this;
    }

    /**
     * Sort the function calls tree from left to right and from bottom to up
     * @param array $statements
     * @param null|int $parent parent statement ID
     * @return array
     */
    public function getSortedStatements(array $statements, $parent = null): array
    {
        if (count($statements) === 0) {
            return [];
        }

        $linkedStatements = [];

        $lastStatement = null;
        foreach ($statements as $statement) {
            if ($statement['parent'] === null) {

                if ($lastStatement === null) {
                    $statement['prev'] = -1;
                    $statement['next'] = -1;
                } else {
                    $statement['prev'] = $lastStatement['id'];
                    $statement['next'] = -1;
                    $linkedStatements[$lastStatement['id']]['next'] = $statement['id'];
                }
                $lastStatement = $statement;
            } else {
                $statement['next'] = $statement['parent'];

                $parentPrevId = $linkedStatements[$statement['parent']]['prev'];

                $statement['prev'] = $parentPrevId;

                $linkedStatements[$parentPrevId]['next'] = $statement['id'];
                $linkedStatements[$statement['parent']]['prev'] = $statement['id'];
            }
            $linkedStatements[$statement['id']] = $statement;
        }

        $statement = $linkedStatements[array_flip(array_column($linkedStatements, 'prev'))[-1]];
        $sortedStatements = [
            $statement
        ];
        while ($statement['next'] !== -1) {
            $statement = $linkedStatements[$statement['next']];
            $sortedStatements[] = $statement;
        }

        return $sortedStatements;
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
     * Parse the template to compute the execution stack
     * @param string $template
     * @return array Array that contains compiledTemplate and executionStack outputs
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    protected function compileTemplate($template)
    {
        $beginFunctionChar = '#';
        $beginArgsChar = '{';
        $endArgsChar = '}';
        $authorizedCharsFuncName = array_flip(array_merge(range('a', 'z'), ['_']));

        $template = preg_split('//u', $template, -1, PREG_SPLIT_NO_EMPTY);

        $compiledTemplate = '';
        $executionStack = [];
        $unclosedFunctionsStack = [];

        $callingFunction = false;
        $callingFunctionName = '';

        $parsingEnded = false;
        $currentCharIndex = 0;
        $currentStackIndex = 0;

        while(!$parsingEnded) {
            if (!isset($template[$currentCharIndex])) {
                $parsingEnded = true;
                continue;
            }

            // Parsing function name
            if ($callingFunction) {
                if ($template[$currentCharIndex] === $beginArgsChar && mb_strlen($callingFunctionName) !== 0) {
                    // End of function name
                    if (!in_array($callingFunctionName, array_keys($this->functions))) {
                        throw new \InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $callingFunctionName));
                    }

                    $compiledTemplate .= '[' . $currentStackIndex . ']';

                    $parent = null;
                    if (count($unclosedFunctionsStack) !== 0) {
                        $parent = $unclosedFunctionsStack[count($unclosedFunctionsStack) - 1];
                    }

                    $executionStack[] = [
                        'id' => $currentStackIndex,
                        'function' => $callingFunctionName,
                        'parent' => $parent,
                    ];

                    $unclosedFunctionsStack[] = $currentStackIndex;

                    $currentStackIndex++;

                    $callingFunction = false;
                    $callingFunctionName = '';
                } elseif (isset($authorizedCharsFuncName[$template[$currentCharIndex]])) {
                    // Function name new char
                    $callingFunctionName .= $template[$currentCharIndex];
                } else {
                    // Wrong char in function name, ignore the call
                    $compiledTemplate .= '#' . $callingFunctionName . $template[$currentCharIndex];
                    $callingFunction = false;
                    $callingFunctionName = '';
                }
            } else {
                if ($template[$currentCharIndex] === $beginFunctionChar) {
                    // Begin of new function call
                    $callingFunction = true;
                    $callingFunctionName = '';
                } elseif ($template[$currentCharIndex] === $endArgsChar) {
                    // End of function call
                    if (($lastUnclosedFunction = array_pop($unclosedFunctionsStack)) !== null) {
                        $compiledTemplate .= '[/' . $lastUnclosedFunction . ']';
                    } else {
                        $compiledTemplate .= $template[$currentCharIndex];
                    }
                } else {
                    $compiledTemplate .= $template[$currentCharIndex];
                }
            }
            $currentCharIndex++;
        }

        return [
            'compiledTemplate' => $compiledTemplate,
            'executionStack' => $executionStack,
        ];
    }

    /**
     * Register a text function
     * @param $name string function name to be used within the template
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

    /**
     * @return string
     */
    public function getCompiledTemplate(): string
    {
        return $this->compiledTemplate;
    }

    /**
     * @param string $compiledTemplate
     */
    public function setCompiledTemplate(string $compiledTemplate): void
    {
        $this->compiledTemplate = $compiledTemplate;
    }

    /**
     * @return array
     */
    public function getExecutionStack(): array
    {
        return $this->executionStack;
    }

    /**
     * @param array $executionStack
     */
    public function setExecutionStack(array $executionStack): void
    {
        $this->executionStack = $executionStack;
    }
}