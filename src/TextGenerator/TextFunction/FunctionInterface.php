<?php

namespace Neveldo\TextGenerator\TextFunction;

/**
 * Interface FunctionInterface
 * Interface for parsers
 * @package Neveldo\TextGenerator\TextFunction
 */
interface FunctionInterface
{
    /**
     * Execute the parser
     * @param array $arguments
     * @return string
     */
    public function execute(array $arguments, array $originalArguments);
}