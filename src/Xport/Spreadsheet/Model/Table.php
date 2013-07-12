<?php

namespace Xport\Spreadsheet\Model;

/**
 * Table
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class Table implements SpreadsheetModel
{
    /**
     * Indicates if column labels must be displayed
     * @var bool
     */
    private $displayColumnsLabel = true;

    /**
     * @var Column[]
     */
    private $columns =[];

    /**
     * @var Line[]
     */
    private $lines =[];

    /**
     * Cells indexed by their coordinate
     * @var Cell[]
     */
    private $cells = [];


    /**
     * @return bool
     */
    public function displayColumnsLabel()
    {
        return $this->displayColumnsLabel;
    }

    /**
     * @param bool $displayColumnsLabel
     */
    public function setDisplayColumnsLabel($displayColumnsLabel)
    {
        $this->displayColumnsLabel = $displayColumnsLabel;
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
     * @return Cell[]
     */
    public function getCells()
    {
        return $this->cells;
    }

    /**
     * @param Line $line
     * @param Column $column
     * @param Cell $cell
     * @throws \InvalidArgumentException
     */
    public function setCell(Line $line, Column $column, Cell $cell)
    {
        $columnKey = false;
        foreach ($this->getColumns() as $key => $tableColumn) {
            if (spl_object_hash($column) === spl_object_hash($tableColumn)) {
                $columnKey = $key;
                break;
            }
        }
        if ($columnKey === false) {
            throw new \InvalidArgumentException("The given 'Column' was not found in the 'Table'.");
        }

        $lineKey = false;
        foreach ($this->getLines() as $key => $tableLine) {
            if (spl_object_hash($line) === spl_object_hash($tableLine)) {
                $lineKey = $key;
                break;
            }
        }
        if ($lineKey === false) {
            throw new \InvalidArgumentException("The given 'Line' was not found in the 'Table'.");
        }

        $this->cells[$columnKey . '&' . $lineKey] = $cell;
    }

    /**
     * @param Line $line
     * @param Column $column
     * @throws \InvalidArgumentException
     * @return Cell
     */
    public function getCell(Line $line, Column $column)
    {
        $columnKey = false;
        foreach ($this->getColumns() as $key => $tableColumn) {
            if (spl_object_hash($column) === spl_object_hash($tableColumn)) {
                $columnKey = $key;
                break;
            }
        }
        if ($columnKey === false) {
            throw new \InvalidArgumentException("The given 'Column' was not found in the 'Table'.");
        }

        $lineKey = false;
        foreach ($this->getLines() as $key => $tableLine) {
            if (spl_object_hash($line) === spl_object_hash($tableLine)) {
                $lineKey = $key;
                break;
            }
        }
        if ($lineKey === false) {
            throw new \InvalidArgumentException("The given 'Line' was not found in the 'Table'.");
        }

        return $this->cells[$columnKey . '&' . $lineKey];
    }

}
