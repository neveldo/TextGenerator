<?php

namespace Neveldo\TextGenerator\TextFunction;

/**
 * Interface FunctionInterface
 * Interface for text functions
 * @package Neveldo\TextGenerator\TextFunction
 */
interface FunctionInterface
{
    /**
     * Execute the function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string;
}
