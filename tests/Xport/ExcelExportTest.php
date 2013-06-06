<?php

namespace Xport;

use Xport\ExcelModel\Cell;
use Xport\ExcelModel\Column;
use Xport\ExcelModel\File;
use Xport\ExcelModel\Line;
use Xport\ExcelModel\Sheet;
use Xport\ExcelModel\Table;

class ExcelExportTest extends \PHPUnit_Framework_TestCase
{
    public function testExcelExport2()
    {
        $exporter = new ExcelExport();

        $excelFile = new File();
        $sheet = new Sheet();
        $excelFile->addSheet($sheet);

        $table = new Table();
        $sheet->addTable($table);

        $col1 = new Column('col1', 'Column 1');
        $table->addColumn($col1);
        $col2 = new Column('col2', 'Column 2');
        $table->addColumn($col2);

        $line1 = new Line('line1');
        $table->addLine($line1);
        $line2 = new Line('line2');
        $table->addLine($line2);

        $table->setCell($line1, $col1, new Cell(10));
        $table->setCell($line1, $col2, new Cell(0.5));
        $table->setCell($line2, $col1, new Cell(20));
        $table->setCell($line2, $col2, new Cell(0.35));

        $exporter->export($excelFile, __DIR__ . '/test.xslx');
    }
}
