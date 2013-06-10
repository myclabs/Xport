<?php

namespace Xport\MappingReader;

use Xport\SpreadsheetModel\Parser\ParsingException;

/**
 * File reader
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class MappingReader
{
    /**
     * @throws ParsingException
     * @return array
     */
    abstract public function getMapping();
}
