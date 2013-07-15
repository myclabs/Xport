<?php

namespace Xport\Spreadsheet\Model;

/**
 * Document
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Document implements SpreadsheetModel
{
    /**
     * @var Sheet[]
     */
    private $sheets = [];

    /**
     * @param Sheet $sheet
     */
    public function addSheet(Sheet $sheet)
    {
        $this->sheets[] = $sheet;
    }

    /**
     * @return Sheet[]
     */
    public function getSheets()
    {
        return $this->sheets;
    }
}
