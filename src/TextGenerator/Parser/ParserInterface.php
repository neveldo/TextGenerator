<?php

namespace Neveldo\TextGenerator\Parser;

/**
 * Interface ParserInterface
 * @package Neveldo\TextGenerator\Parser
 */
interface ParserInterface
{
    /**
     * @param array $arguments
     * @return string
     */
    public function parse(array $arguments);
}