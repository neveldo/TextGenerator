<?php

namespace Neveldo\TextGenerator\TextFunction;

use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class FilterFunction
 * 'filter' function: allow to filter strings or numeric values through PHP native functions
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class FilterFunction implements FunctionInterface
{
    /**
     * @var TagReplacerInterface Tag Replacer service
     */
    private $tagReplacer;

    /**
     * @var array array of available filters
     */
    protected $availableFilters = [];

    /**
     * FilterFunction constructor.
     * @param TagReplacerInterface $tagReplacer
     */
    public function __construct(TagReplacerInterface $tagReplacer)
    {
        $this->tagReplacer = $tagReplacer;

        // Init defaultfilters
        $this->availableFilters = [
            'round' => [
                'function' => 'round',
                'minArgs' => 1,
                'maxArgs' => 3
            ],
            'ceil' => [
                'function' => 'ceil',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'floor' => [
                'function' => 'floor',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'max' => [
                'function' => 'max',
                'minArgs' => 2
            ],
            'min' => [
                'function' => 'min',
                'minArgs' => 2
            ],
            'rand' => [
                'function' => 'rand',
                'minArgs' => 2,
                'maxArgs' => 2
            ],
            'number' => [
                'function' => 'number_format',
                'minArgs' => 1,
                'maxArgs' => 4
            ],

            'lower' => [
                'function' => 'strtolower',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'upper' => [
                'function' => 'strtoupper',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'lowerfirst' => [
                'function' => 'lcfirst',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'upperfirst' => [
                'function' => 'ucfirst',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'upperwords' => [
                'function' => 'ucwords',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'trim' => [
                'function' => 'trim',
                'minArgs' => 1,
                'maxArgs' => 1
            ],
            'substring' => [
                'function' => 'substr',
                'minArgs' => 2,
                'maxArgs' => 3
            ],
            'timestamp' => [
                'function' => function($format, $timestamp = null) {
                    if (isset($timestamp)) {
                        $timestamp = (int) $timestamp;
                    } else {
                        $timestamp = time();
                    }
                    return date($format, $timestamp);
                },
                'minArgs' => 1,
                'maxArgs' => 2
            ],
            'date' => [
                'function' => function($date, $fromFormat, $toFormat) {
                    $datetime = \DateTime::createFromFormat($fromFormat, $date);
                    return $datetime->format($toFormat);
                },
                'minArgs' => 3,
                'maxArgs' => 3
            ],
        ];
    }

    /**
     * Handle Filter function
     * @param array $arguments list of arguments where tags have been replaced by their values
     * @param array $originalArguments list of original arguments
     * @return string
     * @throw InvalidArgumentException if the number of arguments is not valid
     */
    public function execute(array $arguments, array $originalArguments)
    {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException(
                sprintf("FilterFunction expect at least two parameters, %d given.", count($arguments))
            );
        }

        if (!isset($this->availableFilters[$arguments[0]])) {
            throw new \InvalidArgumentException(
                sprintf("The filter %s is not available.", $arguments[0])
            );
        }

        $filterName = $arguments[0];
        $filter = $this->availableFilters[$filterName];
        array_shift($arguments);

        if (count($arguments) < $filter['minArgs']) {
            throw new \InvalidArgumentException(
                sprintf("Filter %s expect at least %d parameters, %d given.", $filterName, $filter['minArgs'], (count($arguments)))
            );
        }

        if (isset($filter['maxArgs']) && count($arguments) > $filter['maxArgs']) {
            throw new \InvalidArgumentException(
                sprintf("Filter %s expect maximum %d parameters, %d given.", $filterName, $filter['maxArgs'], (count($arguments)))
            );
        }

        if (is_callable($filter['function'])) {
            return call_user_func_array($filter['function'], $arguments);
        }
        return '';
    }

    /**
     * Add a filter
     * @param $name filter name
     * @param array $filter filter conf array
     */
    public function addFilter($name, array $filter)
    {
        $this->availableFilters[$name] = $filter;
    }

}