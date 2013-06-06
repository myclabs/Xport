<?php

namespace Xport\ExcelModel;

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
     * @param Line $line
     */
    public function addLine(Line $line)
    {
        $this->lines[] = $line;
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

    /**
     * @param Line   $line
     * @param Column $column
     * @param Cell   $cell
     */
    public function setCell(Line $line, Column $column, Cell $cell)
    {
        $coordinates = $line->getId() . '&' . $column->getId();

        $this->cells[$coordinates] = $cell;
    }

    /**
     * @param Line   $line
     * @param Column $column
     * @return Cell
     */
    public function getCell(Line $line, Column $column)
    {
        $coordinates = $line->getId() . '&' . $column->getId();

        return $this->cells[$coordinates];
    }

}
