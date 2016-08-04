<?php

namespace Neveldo\TextGenerator\TextFunction;

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
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * ExprFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;
    }

    /**
     * Handle Expr function
     * @param array $arguments
     * @return string
     * @throw InvalidArgumentException if the number of arguments is not valid
     */
    public function execute(array $arguments)
    {
        if (count($arguments) !== 1) {
            throw new \InvalidArgumentException(
                sprintf("ExprFunction expect exactly one parameter, %d given.", count($arguments))
            );
        }

        return (new ExpressionLanguage())->evaluate($arguments[0], $this->tagReplacer->getTags());
    }

}