<?php

namespace Xport\ExcelModel;

/**
 * Excel file
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class File
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
