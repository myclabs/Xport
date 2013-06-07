<?php

namespace Xport\SpreadsheetModel;

/**
 * Column
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Column
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $cellContent;

    public function __construct($id, $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $cellContent
     */
    public function setCellContent($cellContent)
    {
        $this->cellContent = $cellContent;
    }

    /**
     * @return string
     */
    public function getCellContent()
    {
        return $this->cellContent;
    }
}
