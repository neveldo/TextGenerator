<?php

namespace Neveldo\TextGenerator;

use InvalidArgumentException;
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
    private array $functions = [];

    private TagReplacerInterface $tagReplacer;

    /**
     * @var string the compiled template
     */
    private string $compiledTemplate;

    /**
     * @var array<int, array{'id': int, 'function': string, 'parent': int|null, 'next': int, 'prev': int}> sorted execution stack to run for generating a text
     */
    private array $executionStack = [];

    public function __construct(TagReplacerInterface $tagReplacer = null)
    {
        // Init the tag replacer
        if (!$tagReplacer instanceof TagReplacerInterface) {
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
     * @param array<string, string|array<int,array<string,string>>> $data that will feed the tags within the template
     * @return string the generated text
     * @throw Exception
     */
    public function generate(array $data): string
    {
        if ($this->compiledTemplate === '') {
            return '';
        }

        $this->tagReplacer->setTags($data);

        $text = $this->compiledTemplate;

        // Execute the functions stack starting with the deepest functions and ending
        // with the shallowest ones
        foreach ($this->executionStack as $statement) {

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
                (int) mb_strpos($text, $openingTag),
                (int) mb_strpos($text, $closingTag) + mb_strlen($closingTag) - mb_strpos($text, $openingTag)
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
     */
    protected function substringReplace(string $str, string $replacement, int $start, int $length): string
    {
        return mb_substr($str, 0, $start) . $replacement . mb_substr($str, $start + $length);
    }

    /**
     * Prepare the template by parsing the function calls within it
     * @param string $template The template to compile
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    public function compile(string $template): static
    {
        $template = $this->parseIndentations($template);
        $data = $this->compileTemplate($template);

        $this->executionStack = $this->getSortedStatements($data['executionStack']);
        $this->compiledTemplate = $data['compiledTemplate'];

        return $this;
    }

    /**
     * Sort the function calls tree from left to right and from bottom to up
     * @param array<int, array{'id': int, 'function': string, 'parent': int|null}> $statements
     * @return array<int, array{'id': int, 'function': string, 'parent': int|null, 'next': int, 'prev': int}>
     */
    public function getSortedStatements(array $statements): array
    {
        if ($statements === []) {
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
        /** @phpstan-ignore return.type */
        return $sortedStatements;
    }

    /**
     * Remove the ;; followed by any space characters from the template
     */
    public function parseIndentations(string $template): string
    {
        return (string) preg_replace('/;;\s+/m', '', $template);
    }

    /**
     * Parse the template to compute the execution stack
     * @param string $template
     * @return array{'compiledTemplate': string, 'executionStack': array<int, array{'id': int, 'function': string, 'parent': int|null}>} Array that contains compiledTemplate and executionStack outputs
     * @throw \InvalidArgumentException if the template contains unknown functions
     */
    protected function compileTemplate(string $template): array
    {
        $beginFunctionChar = '#';
        $beginArgsChar = '{';
        $endArgsChar = '}';
        $authorizedCharsFuncName = array_flip(array_merge(range('a', 'z'), ['_']));

        $template = preg_split('//u', $template, -1, PREG_SPLIT_NO_EMPTY);

        $compiledTemplate = '';
        /** @®var array<int, array{'id': int, 'function': string, 'parent': int|null}> */
        $executionStack = [];
        /** @var array<int,int> */
        $unclosedFunctionsStack = [];

        $callingFunction = false;
        $callingFunctionName = '';

        $parsingEnded = false;
        $currentCharIndex = 0;
        $currentStackIndex = 0;

        while (!$parsingEnded) {
            if (!isset($template[$currentCharIndex])) {
                $parsingEnded = true;
                continue;
            }

            // Parsing function name
            if ($callingFunction) {
                if ($template[$currentCharIndex] === $beginArgsChar && mb_strlen($callingFunctionName) !== 0) {
                    // End of function name
                    if (!in_array($callingFunctionName, array_keys($this->functions))) {
                        throw new InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $callingFunctionName));
                    }

                    $compiledTemplate .= '[' . $currentStackIndex . ']';

                    $parent = null;
                    if ($unclosedFunctionsStack !== []) {
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
            } elseif ($template[$currentCharIndex] === $beginFunctionChar) {
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
    public function registerFunction(string $name, FunctionInterface $function): static
    {
        $this->functions[$name] = $function;
        return $this;
    }

    /**
     * Get a function from its name
     * @return FunctionInterface
     * @throw \InvalidArgumentException if the function doesn't exist
     */
    public function getFunction(string $name): FunctionInterface
    {
        if (!array_key_exists($name, $this->functions)) {
            throw new InvalidArgumentException(sprintf("Error : function '%s' doesn't exist.", $name));
        }
        return $this->functions[$name];
    }

    /**
     * Set the tag replacer
     * @return $this
     */
    public function setTagReplacer(TagReplacerInterface $tagReplacer): static
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

    public function getCompiledTemplate(): string
    {
        return $this->compiledTemplate;
    }

    public function setCompiledTemplate(string $compiledTemplate): void
    {
        $this->compiledTemplate = $compiledTemplate;
    }

    /**
     * @return array<int, array{'id': int, 'function': string, 'parent': int|null, 'next': int, 'prev': int}> sorted execution stack to run for generating a text
     */
    public function getExecutionStack(): array
    {
        return $this->executionStack;
    }

    /**
     * @param array<int, array{'id': int, 'function': string, 'parent': int|null, 'next': int, 'prev': int}> $executionStack sorted execution stack to run for generating a text
     */
    public function setExecutionStack(array $executionStack): void
    {
        $this->executionStack = $executionStack;
    }
}
