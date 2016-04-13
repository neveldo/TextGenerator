<?php

namespace Neveldo\TextGenerator\Parser;

/**
 * Interface ParserInterface
 * Interface for parsers
 * @package Neveldo\TextGenerator\Parser
 */
interface ParserInterface
{
    /**
     * Execute the parser
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments);
}