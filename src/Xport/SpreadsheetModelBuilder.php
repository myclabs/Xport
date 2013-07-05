<?php

namespace Xport;

use Xport\MappingReader\MappingReader;
use Xport\SpreadsheetModel\Cell;
use Xport\SpreadsheetModel\Column;
use Xport\SpreadsheetModel\Parser\ForEachParser;
use Xport\SpreadsheetModel\Parser\ParsingException;
use Xport\SpreadsheetModel\Parser\Scope;
use Xport\SpreadsheetModel\Parser\TwigParser;
use Xport\SpreadsheetModel\SpreadsheetModel;
use Xport\SpreadsheetModel\Line;
use Xport\SpreadsheetModel\Sheet;
use Xport\SpreadsheetModel\Table;

/**
 * Builds a Spreadsheet model
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SpreadsheetModelBuilder
{
    /**
     * @var Scope
     */
    private $scope;
    /**
     * @var ForEachParser
     */
    private $forEachParser;
    /**
     * @var TwigParser
     */
    private $twigParser;

    public function __construct()
    {
        $this->scope = new Scope();
        $this->forEachParser = new ForEachParser();
        $this->twigParser = new TwigParser();
    }

    /**
     * Bind a value to a name.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function bind($name, $value)
    {
        $this->scope->bind($name, $value);
    }

    /**
     * Build a model.
     *
     * @param MappingReader $mappingReader
     * @throws ParsingException
     * @return SpreadsheetModel
     */
    public function build(MappingReader $mappingReader)
    {
        $model = new SpreadsheetModel();
        $this->parseRoot($model, $mappingReader->getMapping(), $this->scope);

        return $model;
    }

    private function parseRoot(SpreadsheetModel $model, $yamlRoot, Scope $scope)
    {
        if (!array_key_exists('sheets', $yamlRoot)) {
            return;
        }

        foreach ($yamlRoot['sheets'] as $yamlSheet) {
            // foreach
            if (array_key_exists('foreach', $yamlSheet)) {
                // Parse the foreach expression
                $sheetScopes = $this->forEachParser->parse($yamlSheet['foreach'], $scope);

                foreach ($sheetScopes as $sheetScope) {
                    $this->parseSheet($model, $yamlSheet, $sheetScope);
                }
            } else {
                $this->parseSheet($model, $yamlSheet, $scope);
            }
        }
    }

    private function parseSheet(SpreadsheetModel $model, $yamlSheet, Scope $scope)
    {
        $sheet = new Sheet();
        $model->addSheet($sheet);

        if (array_key_exists('label', $yamlSheet)) {
            $label = $this->twigParser->parse($yamlSheet['label'], $scope);
            $sheet->setLabel($label);
        }

        $this->parseTables($sheet, $yamlSheet, $scope);
    }

    private function parseTables(Sheet $sheet, $yamlSheet, Scope $sheetScope)
    {
        if (!array_key_exists('tables', $yamlSheet)) {
            return;
        }

        foreach ($yamlSheet['tables'] as $yamlTable) {
            // foreach
            if (array_key_exists('foreach', $yamlTable)) {
                // Parse the foreach expression
                $tableScopes = $this->forEachParser->parse($yamlTable['foreach'], $sheetScope);

                foreach ($tableScopes as $tableScope) {
                    $this->parseTable($sheet, $yamlTable, $tableScope);
                }
            } else {
                $this->parseTable($sheet, $yamlTable, $sheetScope);
            }
        }
    }

    private function parseTable(Sheet $sheet, $yamlTable, Scope $tableScope)
    {
        if (!isset($yamlTable) || !array_key_exists('columns', $yamlTable)) {
            throw new ParsingException("'table' must contain 'columns'");
        }
        if (!isset($yamlTable) || !array_key_exists('lines', $yamlTable)) {
            throw new ParsingException("'table' must contain 'lines'");
        }

        $table = new Table();
        $sheet->addTable($table);

        // Columns
        foreach ($yamlTable['columns'] as $columnIndex => $yamlColumnItem) {
            $columnLabel = $this->twigParser->parse($yamlColumnItem['label'], $tableScope);

            $column = new Column($columnIndex, $columnLabel);
            $column->setCellContent($yamlColumnItem['cellContent']);
            $table->addColumn($column);
        }

        // Lines
        $forEachExpression = $yamlTable['lines']['foreach'];

        $lineScopes = $this->forEachParser->parse($forEachExpression, $tableScope);

        foreach ($lineScopes as $lineIndex => $lineScope) {
            $this->createLine($lineIndex, $table, $lineScope);
        }
    }

    private function createLine($lineIndex, Table $table, Scope $lineScope)
    {
        // Add the line
        $line = new Line($lineIndex);
        $table->addLine($line);

        // Create cells
        foreach ($table->getColumns() as $column) {
            $cell = new Cell();

            $cellContent = $this->twigParser->parse($column->getCellContent(), $lineScope);
            $cell->setContent($cellContent);

            $table->setCell($line, $column, $cell);
        }
    }
}
