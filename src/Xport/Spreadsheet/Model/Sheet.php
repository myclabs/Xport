<?php

namespace Xport\Spreadsheet\Model;

/**
 * Sheet
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Sheet implements SpreadsheetModel
{
    /**
     * @var string|null
     */
    private $label;

    /**
     * @var Table[]
     */
    private $tables = [];

    /**
     * @param string|null $label Sheet label
     */
    public function __construct($label = null)
    {
        $this->label = $label;
    }

    /**
     * @param Table $table
     */
    public function addTable(Table $table)
    {
        $this->tables[] = $table;
    }

    /**
     * @return Table[]
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return null|string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
