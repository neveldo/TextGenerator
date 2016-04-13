<?php

namespace Neveldo\TextGenerator\Parser;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class IfParser
 * Parser for 'if' function: handle conditions a return the "then statement" or the "else statement"
 * depending on the condition evaluation
 * Examples :
 * if{@val = 5|then statement}
 * if{@val = 5|then statement|else statement}
 * if{@val < 5 or %val% > 15|then statement|else statement}
 * if{@val > 5 and val < 15|then statement|else statement
 *
 * @package Neveldo\TextGenerator\Parser
 */
class IfParser implements ParserInterface
{
    /**
     * @var TagReplacerInterface
     */
    private $tagReplacer;

    /**
     * IfParser constructor.
     * @param TagReplacerInterface $tr
     */
    public function __construct(TagReplacerInterface $tr)
    {
        $this->tagReplacer = $tr;
    }

    /**
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments)
    {
        if (count($arguments) !== 2 && count($arguments) !== 3) {
            Throw new \InvalidArgumentException(
                sprintf("IfParser expect exactly two (condition, then statement) or three (condition, then statement, else statement) parameters, %d given.", count($arguments))
            );
        }

        $language = new ExpressionLanguage();
        $condition = str_replace(
            array_keys($this->tagReplacer->getEscapedTags()),
            array_keys($this->tagReplacer->getTags()),
            $arguments[0]
        );

        if ($language->evaluate($condition, $this->tagReplacer->getTags())) {
            return $arguments[1];
        } else if (isset($arguments[2])) {
            return $arguments[2];
        }
        return '';
    }

}