<?php

namespace Xport\Spreadsheet\Model;

/**
 * Column
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Column implements SpreadsheetModel
{
    /**
     * @var string
     */
    private $label;

    /**
     * @param string $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
