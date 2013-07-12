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
                $phpExcelSheet->setTitle($sheet->getLabel());
            }

            $lineOffset = 1;

            // Tables
            foreach ($sheet->getTables() as $table) {

                // Columns
                foreach ($table->getColumns() as $columnIndex => $column) {
                    // Column header
                    if ($table->displayColumnsLabel()) {
                        $phpExcelSheet->setCellValueByColumnAndRow($columnIndex, $lineOffset, $column->getLabel());
                    }

                    // Lines
                    foreach ($table->getLines() as $lineIndex => $line) {
                        // Cell
                        $cell = $table->getCell($line, $column);
                        $phpExcelSheet->setCellValueByColumnAndRow(
                            $columnIndex,
                            $lineOffset + 1 + $lineIndex,
                            $cell->getContent()
                        );
                    }
                }

                $lineOffset += 1 + count($table->getLines());
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