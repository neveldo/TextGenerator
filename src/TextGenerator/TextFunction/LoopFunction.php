<?php

namespace Neveldo\TextGenerator\TextFunction;

use InvalidArgumentException;
use Neveldo\TextGenerator\Tag\TagReplacer;
use Neveldo\TextGenerator\Tag\TagReplacerInterface;

/**
 * Class LoopFunction
 * 'loop' function
 * arguments list :
 *   - the tag to loop on
 *   - number of items to loop on ('*' to loop on all elements)
 *   - shuffle the items (true/false)
 *   - separator between each item
 *   - separator for the last item
 *   - the template for each item
 * Example :
 * 'tag_name contains the array [['name' => 'Bill'], ['name' => 'Bob']]
 * #loop{tag_name|*|true|, | and |hello @name}
 * will output : hello Bill and hello Bob
 *
 * @package Neveldo\TextGenerator\TextFunction
 */
class LoopFunction implements FunctionInterface
{
    public function __construct(private readonly TagReplacerInterface $tagReplacer)
    {
    }

    /**
     * Handle Loop function
     * @param array<int,string> $arguments list of arguments where tags have been replaced by their values
     * @param array<int,string> $originalArguments list of original arguments
     */
    public function execute(array $arguments, array $originalArguments): string
    {
        if (count($arguments) !== 6) {
            throw new InvalidArgumentException(
                sprintf("Loop expect exactly six parameters, %d given.", count($arguments))
            );
        }

        // Parse argument 0 : the tag that contain the data to loop on
        $loopData = $this->tagReplacer->getTag($originalArguments[0]);
        if (!is_array($loopData)) {
            return TagReplacer::EMPTY_TAG;
        }

        $loopStrings = [];
        foreach ($loopData as $tags) {
            $tagReplacer = clone $this->tagReplacer;
            $tagReplacer->setTags($tags);
            $loopStrings[] = $tagReplacer->replace($arguments[5]);
        }

        // Remove empty strings and arguments that contain empty tags
        $loopStrings = array_filter($loopStrings, fn ($item): bool => $item !== '' && mb_strpos($item, $this->tagReplacer->getEmptyTag()) === false);

        // Parse argument 1 : number of items to loop on ('*' to loop on all elements)
        $limit = count($loopStrings);

        if ($arguments[1] !== '*') {
            $limit = min($limit, (int) $arguments[1]);
        }

        // Parse argument 2 : shuffle the items (true/false)
        if (mb_strtolower((string) $arguments[2]) === 'true') {
            shuffle($loopStrings);
        }

        $loopStrings = array_values($loopStrings);

        // Concatenate the strings with the proper separator
        $result = '';
        for ($i = 0; $i < $limit; ++$i) {
            $result .= $loopStrings[$i];

            if ($i < $limit - 2) {
                $result .= $arguments[3];
            } elseif ($i < $limit - 1) {
                $result .= $arguments[4];
            }
        }

        return $result;
    }
}
