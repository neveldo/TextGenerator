<?php

namespace Neveldo\TextGenerator\Parser;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class IfParser
 * Parser for 'if' function: handle conditions a return the "then statement" or the "else statement"
 * depending on the condition evaluation
 * Examples :
 * #if{%val% = 5|then statement}
 * #if{%val% = 5|then statement|else statement}
 * #if{%val% < 5 or %val% > 15|then statement|else statement}
 * #if{%val% > 5 and %val% < 15 or %val2% > 10 and %val2% < 30|then statement|else statement
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
    public function __construct($tr)
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
            Throw new \RuntimeException(
                sprintf("ShuffleParser expect exactly two (condition, then statement) or three (condition, then statement, else statement), %d given.", count($arguments))
            );
        }

        $orTokens = preg_split("/or/i", $arguments[0]);

        foreach($orTokens as $orToken) {
            $andTokens = preg_split("/and/i", $orToken);

            $andResult = true;
            foreach($andTokens as $andToken) {
                preg_match('/(.+)\s+(<=|>=|=|<>|<|>)\s+(.+)/', $andToken, $matches);

                $op1 = $this->parseOperande($matches[1]);
                $op2 = $this->parseOperande($matches[3]);

                switch ($matches[2]) {
                    case '<=' :
                        if (!($op1 <= $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                    case '>=' :
                        if (!($op1 >= $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                    case '=' :
                        if (!($op1 == $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                    case '<>' :
                        if (!($op1 != $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                    case '<' :
                        if (!($op1 < $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                    case '>' :
                        if (!($op1 > $op2)) {
                            $andResult = false;
                            break;
                        }
                        break;
                }
            }

            if ($andResult) {
                return trim($arguments[1]);
            }
        }

        if (isset($arguments[2])) {
            return trim($arguments[2]);
        } else {
            return '';
        }
    }

    /**
     * @param string $op
     * @return float|string
     */
    private function parseOperande($op)
    {
        $op = trim($this->tagReplacer->replace($op));
        if (is_numeric($op)) {
            $op = (float) $op;
        }
        if ($op === "''" || $op === '""') {
            $op = '';
        }

        return $op;
    }

}