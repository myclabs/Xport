<?php

namespace Xport\Spreadsheet\Exporter;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Worksheet;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Writer_IWriter;
use Xport\Spreadsheet\Model\Document;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;

/**
 * Exports an Spreadsheet model to an Excel file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author valentin-mcs <valentin.claras@myc-sense.com>
 */
class PHPExcelExporter
{
    private $tableLabelStyle = [
        'borders'   => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_HAIR,
            ],
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ],
        'font'      => [
            'italic' => true,
        ],
    ];

    private $columnHeaderStyle = [
        'borders' => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_HAIR,
            ],
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        ],
        'font' => [
            'bold' => true,
        ],
    ];

    private $cellBorderStyle = [
        'borders' => [
            'allborders' => [
                'style' => \PHPExcel_Style_Border::BORDER_HAIR,
            ],
        ],
    ];

    /**
     * Exports an Spreadsheet model to a file.
     *
     * @param Document                     $model
     * @param string                       $targetFile
     * @param PHPExcel_Writer_IWriter|null $writer Writer allowing to choose which file format to use
     */
    public function export(Document $model, $targetFile, PHPExcel_Writer_IWriter $writer = null)
    {
        $phpExcelModel = new PHPExcel();

        foreach ($model->getSheets() as $sheetIndex => $sheet) {
            if ($sheetIndex > $phpExcelModel->getSheetCount() - 1) {
                $phpExcelModel->createSheet();
            }

            $phpExcelSheet = $phpExcelModel->getSheet($sheetIndex);

            $this->processSheet($sheet, $phpExcelSheet);
        }

        if (count($model->getSheets()) > 0) {
            $phpExcelModel->setActiveSheetIndex(0);
        }

        $this->writeToFile($phpExcelModel, $targetFile, $writer);
    }

    private function processSheet(Sheet $sheet, PHPExcel_Worksheet $phpExcelSheet)
    {
        if ($sheet->getLabel()) {
            $phpExcelSheet->setTitle(substr($sheet->getLabel(), 0, 31));
        }

        // Process tables
        $lineOffset = 1;
        $maxColumnCount = 0;
        foreach ($sheet->getTables() as $table) {
            $this->processTable($table, $phpExcelSheet, $lineOffset);

            $lineOffset += count($table->getLines());
            // Add an empty line after each table
            $lineOffset ++;

            $maxColumnCount = max($maxColumnCount, count($table->getColumns()));
        }

        // Set auto size for col's width
        for ($columnIndex = 0; $columnIndex <= $maxColumnCount; $columnIndex++) {
            $phpExcelSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }
    }

    private function processTable(Table $table, PHPExcel_Worksheet $phpExcelSheet, &$lineOffset)
    {
        $tableStartingLineIndex = $lineOffset;
        $tableStartingColumnIndex = PHPExcel_Cell::stringFromColumnIndex(0);
        $tableEndingColumnIndex = PHPExcel_Cell::stringFromColumnIndex(count($table->getColumns()) - 1);

        // Table label
        if ($table->getLabel() !== null) {
            $phpExcelSheet->setCellValueByColumnAndRow(0, $lineOffset, $table->getLabel());
            if (count($table->getColumns()) > 1) {
                $phpExcelSheet->mergeCells(
                    $tableStartingColumnIndex . $lineOffset . ':' . $tableEndingColumnIndex . $lineOffset
                );
            }
            $lineOffset ++;
        }

        // Header.
        if ($table->displayColumnsLabel()) {
            $lineOffset++;
        }

        // Columns
        foreach ($table->getColumns() as $columnIndex => $column) {
            // Column header
            if ($table->displayColumnsLabel()) {
                $phpExcelSheet->setCellValueByColumnAndRow($columnIndex, ($lineOffset - 1), $column->getLabel());
            }

            // Lines
            foreach ($table->getLines() as $lineIndex => $line) {
                // Cell
                $cell = $table->getCell($line, $column);
                if ($cell !== null) {
                    $phpExcelSheet->setCellValueByColumnAndRow(
                        $columnIndex,
                        $lineOffset + $lineIndex,
                        $cell->getContent()
                    );
                }

            }
        }

        // Style.
        //@todo for now, styles are fixed: make them configurable

        // Style for Table label.
        if ($table->getLabel() !== null) {
            $cellCoordinates = $tableStartingColumnIndex . $tableStartingLineIndex;
            if (count($table->getColumns()) > 1) {
                $cellCoordinates .= ':' . $tableEndingColumnIndex . $tableStartingLineIndex;
            }

            $phpExcelSheet->getStyle($cellCoordinates)->applyFromArray($this->tableLabelStyle);

            $tableStartingLineIndex++;
        }

        // Style for column header.
        if ($table->displayColumnsLabel()) {
            $cellCoordinates = $tableStartingColumnIndex . $tableStartingLineIndex .
                ':' . $tableEndingColumnIndex . $tableStartingLineIndex;
            $phpExcelSheet->getStyle($cellCoordinates)->applyFromArray($this->columnHeaderStyle);
            $tableStartingLineIndex++;
        }

        // Border around each lines.
        foreach ($table->getLines() as $lineIndex => $line) {
            $lineNumber = $tableStartingLineIndex + $lineIndex;
            $cellCoordinates = $tableStartingColumnIndex . $lineNumber . ':' . $tableEndingColumnIndex . $lineNumber;
            $phpExcelSheet->getStyle($cellCoordinates)->applyFromArray($this->cellBorderStyle);
        }
    }

    private function writeToFile(PHPExcel $phpExcelModel, $targetFile, PHPExcel_Writer_IWriter $writer = null)
    {
        // Default format is xlsx
        if (!$writer) {
            $writer = new PHPExcel_Writer_Excel2007();
        }

        $writer->setPHPExcel($phpExcelModel);
        $writer->save($targetFile);
    }
}
