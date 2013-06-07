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
        $items = explode(' as ', $str);

        if (!$items || (count($items) !== 2)) {
            throw new ParsingException("Error while parsing '$str', should be in the form 'var1 as var2'");
        }

        $result = [
            'array' => $items[0],
            'value' => $items[1],
        ];

        return $result;
    }
}
