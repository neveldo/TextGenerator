<?php

namespace Neveldo\TextGenerator\ExpressionLanguage;

/**
 * Class ExpressionLanguage
 * Handle tags replacement within a text
 * @package Neveldo\TextGenerator\ExpressionLanguage
 */
class ExpressionLanguage extends \Symfony\Component\ExpressionLanguage\ExpressionLanguage
{
    protected function registerFunctions()
    {
        // Prevent from registering constant() function by default,it could be a security issue ...
        return;
    }
}
