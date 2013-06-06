<?php

namespace Xport;

use PHPExcel_Writer_Excel2007;
use Xport\ExcelModel\File;

/**
 * Exports an Excel model to an Excel file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ExcelExporter
{
    /**
     * Exports an Excel model to an Excel file.
     *
     * @param File   $model
     * @param string $targetFile
     */
    public function export(File $model, $targetFile)
    {
        $excel = new \PHPExcel();

        foreach ($model->getSheets() as $sheetIndex => $sheet) {
            if ($sheetIndex > $excel->getSheetCount() - 1) {
                $excel->createSheet();
            }

            $excelSheet = $excel->getSheet($sheetIndex);
            if ($sheet->getLabel()) {
                $excelSheet->setTitle($sheet->getLabel());
            }

            $lineOffset = 1;

            // Tables
            foreach ($sheet->getTables() as $table) {

                // Columns
                foreach ($table->getColumns() as $columnIndex => $column) {
                    // Column header
                    $excelSheet->setCellValueByColumnAndRow($columnIndex, $lineOffset, $column->getLabel());

                    // Lines
                    foreach ($table->getLines() as $lineIndex => $line) {
                        // Cell
                        $cell = $table->getCell($line, $column);
                        $excelSheet->setCellValueByColumnAndRow(
                            $columnIndex,
                            $lineOffset + 1 + $lineIndex,
                            $cell->getContent()
                        );
                    }
                }

                $lineOffset += 1 + count($table->getLines());
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($excel);
        $objWriter->save($targetFile);
    }
}
