<?php

namespace Xport\Spreadsheet\Reader;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_IReader;
use PHPExcel_Worksheet;
use Xport\Spreadsheet\Model\Cell;
use Xport\Spreadsheet\Model\Column;
use Xport\Spreadsheet\Model\Document;
use Xport\Spreadsheet\Model\Line;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;

/**
 * Read a Spreadsheet model from an Excel file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PHPExcelReader implements Reader
{
    /**
     * @var Document
     */
    private $document;

    /**
     * @var PHPExcel
     */
    private $phpExcel;

    /**
     * @var Sheet
     */
    private $currentSheet;

    /**
     * @var int
     */
    private $currentLineOffset = 1;

    /**
     * @var PHPExcel_Worksheet
     */
    private $currentPHPExcelSheet;

    /**
     * {@inheritdoc}
     */
    public function open($file, PHPExcel_Reader_IReader $reader = null)
    {
        $reader = $reader ?: new PHPExcel_Reader_Excel2007();
        $reader->setReadDataOnly(true);

        $this->phpExcel = $reader->load($file);
        $this->document = new Document();
    }

    /**
     * {@inheritdoc}
     */
    public function openNextSheet()
    {
        // Not the first sheet: open the next one
        if ($this->currentPHPExcelSheet) {
            $this->phpExcel->setActiveSheetIndex($this->phpExcel->getActiveSheetIndex() + 1);
        }
        $this->currentPHPExcelSheet = $this->phpExcel->getActiveSheet();

        $this->currentSheet = new Sheet($this->currentPHPExcelSheet->getTitle());
        $this->document->addSheet($this->currentSheet);

        $this->currentLineOffset = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function readNextTable($hasLabel, $hasColumnHeaders)
    {
        $tableRange = $this->findNextTableRange();
        if ($tableRange === null) {
            throw new ReadingException("No more table to read in sheet " . $this->currentSheet->getLabel());
        }
        $rows = $this->currentPHPExcelSheet->rangeToArray($tableRange);

        $table = new Table();
        
        $currentOffset = 0;
        
        // Table label
        if ($hasLabel) {
            $table->setLabel($rows[$currentOffset][0]);
            $currentOffset++;
        }
        
        // Column headers
        if ($hasColumnHeaders) {
            $headers = $rows[$currentOffset];
            $currentOffset++;
            foreach ($headers as $columnHeader) {
                $table->addColumn(new Column($columnHeader));
            }
        }

        // Lines
        for ($i = $currentOffset; $i < count($rows); $i++) {
            $table->addLine(new Line());
        }
        
        // Cells
        for ($i = $currentOffset; $i < count($rows); $i++) {
            $row = $rows[$i];
            $rowIndex = $i - $currentOffset;
            foreach ($row as $columnIndex => $cellContent) {
                $line = $table->getLines()[$rowIndex];
                $column = $table->getColumns()[$columnIndex];
                $cell = new Cell($cellContent);
                $table->setCell($line, $column, $cell);
            }
        }

        $this->currentSheet->addTable($table);

        $this->currentLineOffset += count($rows) + 1 /* empty line after the table */;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocument()
    {
        return $this->document;
    }

    private function findNextTableRange()
    {
        // Stroll through the lines until there's a blank line
        $currentLine = $this->currentLineOffset;
        $maxColumn = 0;
        do {
            // Stroll through the cells (columns) until there's a blank cell
            $currentColumn = 0;
            do {
                $currentCellContent = $this->currentPHPExcelSheet->getCellByColumnAndRow($currentColumn, $currentLine);
                $currentColumn++;
            } while ($currentCellContent != null && $this->currentPHPExcelSheet->cellExistsByColumnAndRow($currentColumn, $currentLine));
            $maxColumn = max($maxColumn, $currentColumn - 1);

            $currentLineContent = $this->currentPHPExcelSheet->getCellByColumnAndRow(0, $currentLine);
            $currentLine++;
        } while ($currentLineContent != null && $this->currentPHPExcelSheet->cellExistsByColumnAndRow(0, $currentLine));

        // Last line was empty
        $currentLine--;

        // If we found a table
        if ($currentLine > $this->currentLineOffset) {
            return PHPExcel_Cell::stringFromColumnIndex(0) . $this->currentLineOffset
                . ':' . PHPExcel_Cell::stringFromColumnIndex($maxColumn) . $currentLine;
        }

        return null;
    }
}
