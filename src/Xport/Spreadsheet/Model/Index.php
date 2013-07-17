<?php

namespace Xport\Spreadsheet\Model;

/**
 * Index
 *
 * @author valentin-mcs <valentin.claras@myc-sense.com>
 */
class Index implements SpreadsheetModel
{
    /**
     * @var string
     */
    private $label;

    /**
     * @param string $label
     */
    public function __construct($label=null)
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
