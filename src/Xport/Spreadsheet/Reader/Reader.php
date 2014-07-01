<?php

namespace Xport\Spreadsheet\Reader;

use Xport\Spreadsheet\Model\Document;

/**
 * Read a Spreadsheet model from a file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Reader
{
    /**
     * Read the next sheet.
     * @throws ReadingException
     */
    public function openNextSheet();

    /**
     * Read the next table.
     * @param boolean $hasLabel
     * @param boolean $hasColumnHeaders
     * @throws ReadingException
     */
    public function readNextTable($hasLabel, $hasColumnHeaders);

    /**
     * Returns the document read from the file.
     * @throws ReadingException
     * @return Document
     */
    public function getDocument();
}
