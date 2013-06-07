<?php

namespace Xport\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\ParsingException;

/**
 * "foreach" parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ForEachParser
{
    /**
     * Parse a foreach expression.
     *
     * The expression has the form: 'foo as bar', where foo is an array and bar the value.
     *
     * @param string $str foreach expression
     *
     * @throws ParsingException
     * @return array Keys are 'array' and 'value'
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

    public function parseWithKey($str)
    {
        $result = preg_match('/^\s*([[:alnum:]\.\[\]]+)\s*as\s*([[:alnum:]\.\[\]]+)\s*=>\s*([[:alnum:]\.\[\]]+)\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'key'   => $matches[2],
            'value' => $matches[3],
        ];
    }

    public function parseWithoutKey($str)
    {
        $result = preg_match('/^\s*([[:alnum:]\.\[\]]+)\s*as\s*([[:alnum:]\.\[\]]+)\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'value' => $matches[2],
        ];
    }
}
