<?php

namespace Xport\ExcelModel;

/**
 * Sheet
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Sheet
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
     * @return null|string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
