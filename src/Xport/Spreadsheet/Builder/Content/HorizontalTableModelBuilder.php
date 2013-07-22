<?php

namespace Xport\Spreadsheet\Builder\Content;

use Xport\Parser\Scope;
use Xport\Parser\ParsingException;
use Xport\Parser\TwigExecutor;
use Xport\Spreadsheet\Builder\ModelBuilder;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;
use Xport\Spreadsheet\Model\Line;
use Xport\Spreadsheet\Model\Column;
use Xport\Spreadsheet\Model\Cell;

/**
 * Builds a content model
 *
 * @author valentin-mcs <valentin.claras@myc-sense.com>
 */
class HorizontalTableModelBuilder extends ModelBuilder implements ContentModelBuilder
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
        if (isset($yamlContent) && (isset($yamlContent['label']))) {
            $table->setLabel($yamlContent['label']);
        }

        // Lines.
        if (!isset($yamlContent) || !array_key_exists('lines', $yamlContent)) {
            throw new ParsingException("'content' of type 'HorizontalTable' must contains 'lines'");
        }
        foreach ($yamlContent['lines'] as $yamlLine) {
            $this->parseLine($table, $yamlLine, $scope);
        }

        // Columns.
        if (!isset($yamlContent) || !array_key_exists('columns', $yamlContent)) {
            throw new ParsingException("'content' of type 'HorizontalTable' must contains 'columns'");
        }
        foreach ($yamlContent['columns'] as $yamlColumn) {
            $this->parseColumn($table, $yamlColumn, $scope);
        }
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
        if (!is_array($yamlLine)) {
            $lineLabel = $yamlLine;
        } else {
            if (!array_key_exists('label', $yamlLine)) {
                throw new ParsingException("Each 'lines' from 'HorizontalTable' must contains a 'label'");
            }
            $lineLabel = $yamlLine['label'];
        }

        $line = new Line($this->twigExecutor->parse($lineLabel, $scope));
        $table->addLine($line);
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
        // Add the column
        $column = new Column();
        $table->addColumn($column);

        // Then parse Cells.
        if (is_array($yamlColumn) && array_key_exists('cells', $yamlColumn) && is_array($yamlColumn['cells'])) {
            $lineIndex = 0;
            foreach ($yamlColumn['cells'] as $yamlCell) {
                $lineIndex += $this->parseCell($table, $column, $lineIndex, $yamlCell, $scope);
            }
        }
    }

    protected function parseCell(Table $table, Column $column, $lineIndex, $yamlCell, Scope $scope, $lineIteration = 0)
    {
        $lines = $table->getLines();

        if (is_array($yamlCell) && array_key_exists('foreach', $yamlCell)) {
            return $this->parseForeach($yamlCell, $scope, [$this, 'parseCell'], [$table, $column, $lineIndex]);
        } else {
            $this->createCell($table, $column, $lines[$lineIndex + $lineIteration], $yamlCell, $scope);
            return 1;
        }
    }

    protected function createCell(Table $table, Column $column, Line $line, $yamlCell, Scope $scope)
    {
        if (!is_array($yamlCell)) {
            $content = $yamlCell;
        } else {
            if (!array_key_exists('cellContent', $yamlCell)) {
                throw new ParsingException("Each 'cells' from 'HorizontalTable' must contains a 'cellContent'");
            }
            $content = $yamlCell['cellContent'];
        }

        $cell = new Cell();
        $cell->setContent($this->twigExecutor->parse($content, $scope));
        $table->setCell($column, $line, $cell);
    }
}
