<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
use Exception;
use Error;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;
use Neveldo\TextGenerator\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ExprFunction
 * 'expr' function: handle expressions
 * More information about the syntax for the expressions : http://symfony.com/doc/current/components/expression_language/syntax.html
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class ExprFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle Expr function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     * @return string
     * @throw InvalidArgumentException if the number of arguments is not valid
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) !== 1) {
            throw new InvalidArgumentException(
                sprintf("ExprFunction expect exactly one parameter, %d given.", count($arguments))
            );
        }

        try {
            /** @phpstan-ignore return.type */
            return (new ExpressionLanguage())->evaluate(
                $this->tagReplacer->sanitizeTagNames($originalArguments[0]),
                $this->tagReplacer->getTags()
            );
        } catch (Exception|Error) {
            return TagReplacer::EMPTY_TAG;
        }
    }

}
