<?php

namespace Xport;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Parser;
use Xport\SpreadsheetModel\Cell;
use Xport\SpreadsheetModel\Column;
use Xport\SpreadsheetModel\SpreadsheetModel;
use Xport\SpreadsheetModel\Line;
use Xport\SpreadsheetModel\Sheet;
use Xport\SpreadsheetModel\Table;

/**
 * Excel export
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SpreadsheetModelBuilder
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function build($mappingFile, $dataSource)
    {
        $yaml = file_get_contents($mappingFile);

        $yamlReader = new Parser();
        $yamlStructure = $yamlReader->parse($yaml);

        $file = new SpreadsheetModel();
        $this->parseItem($file, $yamlStructure, $dataSource);

        return $file;
    }

    private function parseItem($excelItem, $yamlItem, $dataSource)
    {
        foreach ($yamlItem as $key => $yamlSubItem) {
            // forEach
            if (strpos($key, 'forEach(') !== false) {
                $result = preg_match('/^forEach\(([^\)]+)\)$/', $key, $matches);
                if ($result !== 1 || !isset($matches[1])) {
                    throw new \Exception("Parse error on $key");
                }

                $forEachPropertyPath = $matches[1];

                $this->processForEach($excelItem, $yamlSubItem, $dataSource, $forEachPropertyPath);
            }
            // Sheet
            if ($key === 'sheet') {
                if ($excelItem instanceof SpreadsheetModel) {
                    $sheet = new Sheet();
                    $excelItem->addSheet($sheet);

                    $this->parseItem($sheet, $yamlSubItem, $dataSource);
                } else {
                    throw new \Exception("'sheet' must be at the root of the Excel file");
                }
            }
            // Table
            if ($key === 'table') {
                if ($excelItem instanceof Sheet) {
                    $table = new Table();
                    $excelItem->addTable($table);

                    $this->parseTable($table, $yamlSubItem, $dataSource);
                } else {
                    throw new \Exception("'table' must be in a 'sheet'");
                }
            }
        }
    }

    private function processForEach($excelItem, $yamlItem, $dataSource, $propertyPath)
    {
        $iterator = $this->propertyAccessor->getValue($dataSource, $propertyPath);

        foreach ($iterator as $key => $newDataSource) {
            $this->parseItem($excelItem, $yamlItem, $newDataSource);
        }
    }

    private function parseTable(Table $table, $yamlItem, $dataSource)
    {
        if (!array_key_exists('columns', $yamlItem)) {
            throw new \Exception("'table' must contain 'columns'");
        }

        // Columns
        foreach ($yamlItem['columns'] as $id => $yamlColumnItem) {
            $column = new Column($id, $yamlColumnItem['label']);
            $column->setPath($yamlColumnItem['path']);
            $table->addColumn($column);
        }

        // Lines
        $propertyPath = $yamlItem['lines']['path'];
        $lines = $this->propertyAccessor->getValue($dataSource, $propertyPath);

        foreach ($lines as $i => $lineData) {
            $line = new Line($i);
            $table->addLine($line);

            foreach ($table->getColumns() as $column) {
                $cell = new Cell();

                $cellContent = $this->propertyAccessor->getValue($lineData, $column->getPath());

                $cell->setContent($cellContent);

                $table->setCell($line, $column, $cell);
            }
        }
    }
}
