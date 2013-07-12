<?php

namespace Xport\Spreadsheet\Builder\Content;

use Xport\Parser\Scope;
use Xport\Parser\ParsingException;
use Xport\Parser\TwigExecutor;
use Xport\Spreadsheet\Builder\ModelBuilder;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;
use Xport\Spreadsheet\Model\Column;
use Xport\Spreadsheet\Model\Line;
use Xport\Spreadsheet\Model\Cell;

/**
 * Builds a content model
 *
 * @author valentin-mcs <valentin.claras@myc-sense.fr>
 */
class VerticalTableModelBuilder extends ModelBuilder implements ContentModelBuilder
{
    /**
     * {@inheritdoc}
     * @throws ParsingException
     */
    public function build(Sheet $sheet, $yamlContent, Scope $scope)
    {
        // Init TwigExecutor with all user functions.
        $this->twigExecutor = new TwigExecutor($scope->getFunctions());

        // Table.
        $table = new Table();
        $sheet->addTable($table);

        // Columns.
        if (!isset($yamlContent) || !array_key_exists('columns', $yamlContent)) {
            throw new ParsingException("'content' of type 'VerticalTable' must contains 'columns'");
        }
        foreach ($yamlContent['columns'] as $yamlColumn) {
            $this->parseColumn($table, $yamlColumn, $scope);
        }

        // Lines.
        if (!isset($yamlContent) || !array_key_exists('lines', $yamlContent)) {
            throw new ParsingException("'content' of type 'VerticalTable' must contains 'lines'");
        }
        foreach ($yamlContent['lines'] as $yamlLine) {
            $this->parseLine($table, $yamlLine, $scope);
        }
    }

    protected function parseColumn(Table $table, $yamlColumn, Scope $scope)
    {
        if (is_array($yamlColumn) && array_key_exists('foreach', $yamlColumn)) {
            $this->parseForeach($yamlColumn, $scope, [$this, 'parseColumn'], [$table]);
        } else {
            $this->createColumn($table, $yamlColumn, $scope);
        }
    }

    protected function createColumn(Table $table, $yamlColumn, Scope $scope)
    {
        if (!is_array($yamlColumn)) {
            $columnLabel = $yamlColumn;
        } else {
            if (!array_key_exists('label', $yamlColumn)) {
                throw new ParsingException("Each 'columns' from 'VerticalTable' must contains a 'label'");
            }
            $columnLabel = $yamlColumn['label'];
        }

        $column = new Column($this->twigExecutor->parse($columnLabel, $scope));
        $table->addColumn($column);
    }

    protected function parseLine(Table $table, $yamlLine, Scope $scope)
    {
        if (is_array($yamlLine) && array_key_exists('foreach', $yamlLine)) {
            $this->parseForeach($yamlLine, $scope, [$this, 'parseLine'], [$table]);
        } else {
            $this->createLine($table, $yamlLine, $scope);
        }
    }

    protected function createLine(Table $table, $yamlLine, Scope $scope)
    {
        // Add the line
        $line = new Line();
        $table->addLine($line);

        // Then parse Cells.
        if (is_array($yamlLine) && array_key_exists('cells', $yamlLine) && is_array($yamlLine['cells'])) {
            $columnIndex = 0;
            foreach ($yamlLine['cells'] as $yamlCell) {
                $columnIndex += $this->parseCell($table, $line, $columnIndex, $yamlCell, $scope);
            }
        }
    }

    protected function parseCell(Table $table, Line $line, $columnIndex, $yamlCell, Scope $scope, $columnIteration = 0)
    {
        $columns = $table->getColumns();

        if (is_array($yamlCell) && array_key_exists('foreach', $yamlCell)) {
            $columnIndex += $this->parseForeach($yamlCell, $scope, [$this, 'parseCell'], [$table, $line, $columnIndex]);
        } else {
            $this->createCell($table, $line, $columns[$columnIndex + $columnIteration], $yamlCell, $scope);
            $columnIndex ++;
        }

        return $columnIndex;
    }

    protected function createCell(Table $table, Line $line, Column $column, $yamlCell, Scope $scope)
    {
        if (!is_array($yamlCell)) {
            $content = $yamlCell;
        } else {
            if (!array_key_exists('cellContent', $yamlCell)) {
                throw new ParsingException("Each 'cells' from 'VerticalTable' must contains a 'cellContent'");
            }
            $content = $yamlCell['cellContent'];
        }

        $cell = new Cell();
        $cell->setContent($this->twigExecutor->parse($content, $scope));
        $table->setCell($line, $column, $cell);
    }
}
