<?php

namespace Xport\Spreadsheet\Exporter;

use PHPExcel;
use PHPExcel_Writer_Excel2007;
use PHPExcel_Writer_IWriter;
use Xport\Spreadsheet\Model\Document;

/**
 * Exports an Spreadsheet model to an Excel file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PHPExcelExporter
{
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
            if ($sheet->getLabel()) {
                $phpExcelSheet->setTitle(substr($sheet->getLabel(), 0, 32));
            }

            $lineOffset = 1;
            $lastCol = 0;

            // Tables
            foreach ($sheet->getTables() as $table) {
                $startingLineOffset = $lineOffset;

                if ($table->getLabel() !== null) {
                    $phpExcelSheet->setCellValueByColumnAndRow(
                        0,
                        $lineOffset,
                        $table->getLabel()
                    );
                    if (count($table->getColumns()) > 1) {
                        $phpExcelSheet->mergeCells(
                            \PHPExcel_Cell::stringFromColumnIndex(0).$lineOffset.
                            ':'.
                            \PHPExcel_Cell::stringFromColumnIndex(count($table->getColumns()) - 1).$lineOffset
                        );
                    }
                    $lineOffset ++;
                }

                // Columns
                foreach ($table->getColumns() as $columnIndex => $column) {
                    if ($lastCol < $columnIndex) {
                        $lastCol = $columnIndex;
                    }

                    // Column header
                    if ($table->displayColumnsLabel()) {
                        $phpExcelSheet->setCellValueByColumnAndRow($columnIndex, $lineOffset, $column->getLabel());
                    }

                    // Lines
                    foreach ($table->getLines() as $lineIndex => $line) {
                        // Cell
                        $cell = $table->getCell($line, $column);
                        if ($cell !== null) {
                            $phpExcelSheet->setCellValueByColumnAndRow(
                                $columnIndex,
                                $lineOffset + 1 + $lineIndex,
                                $cell->getContent()
                            );
                        }

                    }
                }

                $lineOffset += 1 + count($table->getLines());

                // Style.
                //@todo move to a StyleBuilder.
                $columnOffset = count($table->getColumns()) - 1;
                // Border and italic for Table label.
                if ($table->getLabel() !== null) {
                    $cellStyle = $phpExcelSheet->getStyle(
                        \PHPExcel_Cell::stringFromColumnIndex(0) . $startingLineOffset
                    );
                    $cellStyle->applyFromArray(
                        [
                            'borders' => [
                                'allborders' => [
                                    'style' => \PHPExcel_Style_Border::BORDER_HAIR
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ],
                            'font' => [
                                'italic' => true
                            ]
                        ]
                    );
                    $startingLineOffset++;
                }
                // Border and bold for column header.
                if ($table->displayColumnsLabel()) {
                    $cellStyle = $phpExcelSheet->getStyle(
                        \PHPExcel_Cell::stringFromColumnIndex(0) . $startingLineOffset
                        . ':' .
                        \PHPExcel_Cell::stringFromColumnIndex($columnOffset) . $startingLineOffset
                    );
                    $cellStyle->applyFromArray(
                        [
                            'borders' => [
                                'allborders' => [
                                    'style' => \PHPExcel_Style_Border::BORDER_HAIR
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            ],
                            'font' => [
                                'bold' => true
                            ]
                        ]
                    );
                    $startingLineOffset++;
                }
                // Border around each lines.
                foreach ($table->getLines() as $lineIndex => $line) {
                    $cellStyle = $phpExcelSheet->getStyle(
                        \PHPExcel_Cell::stringFromColumnIndex(0) . ($startingLineOffset + $lineIndex)
                        . ':' .
                        \PHPExcel_Cell::stringFromColumnIndex($columnOffset) . ($startingLineOffset + $lineIndex)
                    );
                    $cellStyle->applyFromArray(
                        [
                            'borders' => [
                                'allborders' => [
                                    'style' => \PHPExcel_Style_Border::BORDER_HAIR
                                ]
                            ],
                        ]
                    );
                }
                // Add an empty line after each content.
                $lineOffset ++;
            }

            // Set automatic calculation for col's width.
            for ($columnIndex = 0; $columnIndex <= $lastCol; $columnIndex++) {
                $phpExcelSheet->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($columnIndex))->setAutoSize(true);
            }
        }

        $this->writeToFile($phpExcelModel, $targetFile, $writer);
    }

    private function writeToFile(PHPExcel $phpExcelModel, $targetFile, PHPExcel_Writer_IWriter $writer = null)
    {
        if (!$writer) {
            $writer = new PHPExcel_Writer_Excel2007();
        }

        $writer->setPHPExcel($phpExcelModel);
        $writer->save($targetFile);
    }
}
