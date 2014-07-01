<?php

namespace XportTest\Spreadsheet\Reader;

use Xport\Spreadsheet\Model\Table;
use Xport\Spreadsheet\Reader\PHPExcelReader;

class PHPExcelReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testImport()
    {
        $reader = new PHPExcelReader(__DIR__ . '/Fixtures/test1.xls', new \PHPExcel_Reader_Excel5());
        $reader->openNextSheet();
        $reader->readNextTable(false, true);
        $reader->readNextTable(false, true);

        $document = $reader->getDocument();

        $this->assertCount(1, $document->getSheets());
        $sheet1 = $document->getSheets()[0];

        $this->assertEquals('Sheet1', $sheet1->getLabel());
        $this->assertCount(2, $sheet1->getTables());

        $table1 = $sheet1->getTables()[0];
        $this->assertCount(2, $table1->getColumns());
        $this->assertEquals('Header1', $table1->getColumns()[0]->getLabel());
        $this->assertEquals('Header2', $table1->getColumns()[1]->getLabel());
        $this->assertCount(4, $table1->getCells());
        $this->assertCellEquals('value1', $table1, 0, 0);
        $this->assertCellEquals('value2', $table1, 0, 1);
        $this->assertCellEquals('value3', $table1, 1, 0);
        $this->assertCellEquals('value4', $table1, 1, 1);

        $table2 = $sheet1->getTables()[1];
        $this->assertCount(1, $table2->getColumns());
        $this->assertEquals('Header1', $table2->getColumns()[0]->getLabel());
        $this->assertCount(2, $table2->getCells());
        $this->assertCellEquals('value1', $table2, 0, 0);
        $this->assertCellEquals('value2', $table2, 1, 0);
    }

    private function assertCellEquals($expected, Table $table, $rowIndex, $columnIndex)
    {
        $line = $table->getLines()[$rowIndex];
        $column = $table->getColumns()[$columnIndex];
        $this->assertEquals($expected, $table->getCell($line, $column)->getContent());
    }
}
