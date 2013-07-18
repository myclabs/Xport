<?php

namespace Xport\Parser;

use Xport\Parser\ParsingException;

/**
 * "foreach" expression parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ForEachParser
{
    /**
     * Describes the accepted components for the regular expression.
     */
    const ELEMENT_ARRAY_PATTERN = '([[:alnum:]]+[[:alnum:]\\.\\(\\)\\[\\]]*)';
    const ELEMENT_PATTERN = '([[:alnum:]]+)';

    /**
     * Parse a foreach expression.
     *
     * The expression has the form: 'foo as bar', where foo is an array and bar the value.
     *
     * @param string $str foreach expression
     *
     * @throws ParsingException
     * @return array
     */
    public function parse($str)
    {
        $result = $this->parseWithKey($str);

        if (is_array($result)) {
            return $result;
        }

        $result = $this->parseWithoutKey($str);

        if (is_array($result)) {
            return $result;
        }

        throw new ParsingException("Error while parsing '$str', should be in the form 'array as value' or 'array as key => value'");
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     */
    private function parseWithKey($str)
    {
        $result = preg_match('/^\s*'.self::ELEMENT_ARRAY_PATTERN.'\s*as\s*'.self::ELEMENT_PATTERN.'\s*=>\s*'.self::ELEMENT_PATTERN.'\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'key'   => $matches[2],
            'value' => $matches[3],
        ];
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     */
    private function parseWithoutKey($str)
    {
        $result = preg_match('/^\s*'.self::ELEMENT_ARRAY_PATTERN.'\s*as\s*'.self::ELEMENT_PATTERN.'\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'value' => $matches[2],
        ];
    }
}
