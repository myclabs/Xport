<?php

namespace Xport\Excel;

/**
 * Table
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Table
{

    /**
     * @var Line[]
     */
    private $lines =[];

    /**
     * @var Column[]
     */
    private $columns =[];

    /**
     * Cells indexed by their coordinate
     * @var Cell[]
     */
    private $cells = [];

    /**
     * @return Line[]
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
    }

    /**
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

}
