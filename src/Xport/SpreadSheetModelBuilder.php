<?php

namespace Xport;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Parser;
use Xport\SpreadsheetModel\Cell;
use Xport\SpreadsheetModel\Column;
use Xport\SpreadsheetModel\Parser\ForEachParser;
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
class SpreadsheetModelBuilder extends Scope
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;
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
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->forEachParser = new ForEachParser();
        $this->twigParser = new TwigParser();
    }

    public function build($mappingFile)
    {
        $yaml = file_get_contents($mappingFile);

        $yamlReader = new Parser();
        $yamlStructure = $yamlReader->parse($yaml);

        $model = new SpreadsheetModel();
        $this->parseRoot($model, $yamlStructure, $this);

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
                $result = $this->forEachParser->parse($yamlSheet['foreach']);

                $array = $this->propertyAccessor->getValue($scope, $result['array']);

                foreach ($array as $value) {
                    // New sub-scope
                    $subScope = new Scope($scope);
                    $subScope->bind($result['value'], $value);

                    $this->parseSheet($model, $yamlSheet, $subScope);
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
                $result = $this->forEachParser->parse($yamlTable['foreach']);

                $array = $this->propertyAccessor->getValue($sheetScope, $result['array']);

                foreach ($array as $value) {
                    // New sub-scope
                    $tableScope = new Scope($sheetScope);
                    $tableScope->bind($result['value'], $value);

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
            throw new \Exception("'table' must contain 'columns'");
        }
        if (!isset($yamlTable) || !array_key_exists('lines', $yamlTable)) {
            throw new \Exception("'table' must contain 'lines'");
        }

        $table = new Table();
        $sheet->addTable($table);

        // Columns
        foreach ($yamlTable['columns'] as $columnIndex => $yamlColumnItem) {
            $columnLabel = $this->twigParser->parse($yamlColumnItem['label'], $tableScope);

            $column = new Column($columnIndex, $columnLabel);
            $column->setPath($yamlColumnItem['path']);
            $table->addColumn($column);
        }

        // Lines
        $forEachExpression = $yamlTable['lines']['foreach'];

        $result = $this->forEachParser->parse($forEachExpression);
        $lines = $this->propertyAccessor->getValue($tableScope, $result['array']);

        foreach ($lines as $lineIndex => $lineValue) {
            // New sub-scope
            $lineScope = new Scope($tableScope);
            $lineScope->bind($result['value'], $lineValue);

            // Add the line
            $line = new Line($lineIndex);
            $table->addLine($line);

            // Create cells
            foreach ($table->getColumns() as $column) {
                $cell = new Cell();

                $cellContent = $this->propertyAccessor->getValue($lineScope, $column->getPath());

                $cell->setContent($cellContent);

                $table->setCell($line, $column, $cell);
            }
        }
    }
}
