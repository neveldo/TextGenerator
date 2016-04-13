<?php

namespace Neveldo\TextGenerator;

use Neveldo\TextGenerator\Parser\IfParser;
use Neveldo\TextGenerator\Parser\ParserInterface;
use Neveldo\TextGenerator\Parser\RandomParser;
use Neveldo\TextGenerator\Parser\ShuffleParser;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class TextGenerator
 * Allow to generate automated texts from a template and data
 * @package Neveldo\TextGenerator
 */
class TextGenerator
{
    /**
     * @var ParserInterface[] collection of function parsers
     */
    private $parsers = [];

    /**
     * @var TagReplacerInterface
     */
    private $tagReplacer;

    /**
     * @var string the template to handle
     */
    private $template;

    /**
     * TextGenerator constructor.
     * @param TagReplacerInterface|null $tr
     */
    public function __construct(TagReplacerInterface $tr = null)
    {
        // Init the tag replacer
        if ($tr === null) {
            $tr = new TagReplacer();
        }
        $this->tagReplacer = $tr;

        // Init core function parsers
        $this
            ->registerParser('shuffle', new ShuffleParser($this->tagReplacer))
            ->registerParser('random', new RandomParser($this->tagReplacer))
            ->registerParser('if', new IfParser($this->tagReplacer))
        ;
    }

    /**
     * Generate an automated text from data
     * @param array $data data for filling the template
     * @return string the generated text
     */
    public function generate(array $data)
    {
        if ($this->template === null) {
            return '';
        }

        $this->tagReplacer->setTags($data);

        return $this->tagReplacer->replace($this->parse($this->template));
    }

    /**
     * Parse recursively the input for function calls
     * @param $input string|array the input to parse
     * @return string
     */
    private function parse($input)
    {
        if (is_array($input)) {

            // Match the function name and the argment lists
            preg_match('/([a-z_]+)\{(.*)\}/s', $input[0], $matches);

            // Split the arguments list (separator = '|')
            $strArray = preg_split('//u', $matches[2], -1, PREG_SPLIT_NO_EMPTY);

            $depth = 0;
            $arguments = [];
            $currentArgument = '';
            $strSize = count($strArray);
            for ($i = 0; $i < $strSize;++$i) {
                if (($strArray[$i] === '|') && $depth  === 0) {
                    $arguments[] = $currentArgument;
                    $currentArgument = '';
                    continue;
                }

                if ($strArray[$i] === '{') {
                    ++$depth;
                }

                if ($strArray[$i] === '}') {
                    --$depth;
                }

                $currentArgument .= $strArray[$i];

                if ($i === ($strSize - 1)) {
                    $arguments[] = $currentArgument;
                }
            }

            // Call the proper function parser
            $input = $this->getParser($matches[1])->parse($arguments);
        }
        return preg_replace_callback('/([a-z_]+\{(?:[^\{\}]|(?R))+\})/s', [$this, 'parse'], $input);
    }

    /**
     * Register a function parser
     * @param $name function name to be used within the template
     * @param ParserInterface $tp The function parser
     * @return $this
     */
    public function registerParser($name, ParserInterface $tp)
    {
        $this->parsers[$name] = $tp;
        return $this;
    }

    /**
     * Get a parser from its name
     * @param $name
     * @return ParserInterface
     * @Thow \RuntimeException if the parser doesn't exist
     */
    public function getParser($name)
    {
        if (!array_key_exists($name, $this->parsers)) {
            Throw new \RuntimeException(sprintf("Error : parser '%s' doesn't exist.", $name));
        }
        return $this->parsers[$name];
    }

    /**
     * Set the tag replacer
     * @param TagReplacerInterface $tr
     * @return $this
     */
    public function setTagReplacer(TagReplacerInterface $tr)
    {
        $this->tagReplacer = $tr;
        return $this;
    }

    /**
     * return the current tag replacer
     * @return TagReplacerInterface
     */
    public function getTagReplacer()
    {
        return $this->tagReplacer;
    }

    /**
     * Set the template
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (substr_count($template, '{') !== substr_count($template, '}')) {
            Throw new \RuntimeException("Template syntax error, please check functions brackets."
            );
        }
        $this->template = $template;
        return $this;
    }
}