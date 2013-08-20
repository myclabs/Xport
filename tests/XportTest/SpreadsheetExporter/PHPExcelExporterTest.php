<?php

namespace XportTest;

use Xport\Spreadsheet\Exporter\PHPExcelExporter;
use Xport\Spreadsheet\Model\Cell;
use Xport\Spreadsheet\Model\Column;
use Xport\Spreadsheet\Model\Document;
use Xport\Spreadsheet\Model\Line;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;

class PHPExcelExporterTest extends \PHPUnit_Framework_TestCase
{
    public function testExcelExport2()
    {
        $exporter = new PHPExcelExporter();

        $document = new Document();
        $sheet = new Sheet('First sheet');
        $document->addSheet($sheet);
        $document->addSheet(new Sheet('Empty sheet'));

        $table1 = new Table();
        $table1->setLabel('Table 1');
        $sheet->addTable($table1);

        $col1 = new Column('col1', 'Column 1');
        $table1->addColumn($col1);
        $col2 = new Column('col2', 'Column 2');
        $table1->addColumn($col2);

        $line1 = new Line('line1');
        $table1->addLine($line1);
        $line2 = new Line('line2');
        $table1->addLine($line2);

        $table1->setCell($line1, $col1, new Cell(10));
        $table1->setCell($line1, $col2, new Cell(0.5));
        $table1->setCell($line2, $col1, new Cell(20));
        $table1->setCell($line2, $col2, new Cell(0.35));

        $table2 = new Table();
        $table2->setLabel('Table 2');
        $sheet->addTable($table2);

        $col1 = new Column('col1', 'Column 1');
        $table2->addColumn($col1);
        $col2 = new Column('col2', 'Column 2');
        $table2->addColumn($col2);
        $col3 = new Column('col3', 'Column 3');
        $table2->addColumn($col3);
        $col4 = new Column('col4', 'Column 4');
        $table2->addColumn($col4);

        $line1 = new Line('line1');
        $table2->addLine($line1);
        $line2 = new Line('line2');
        $table2->addLine($line2);
        $line3 = new Line('line3');
        $table2->addLine($line3);
        $line4 = new Line('line4');
        $table2->addLine($line4);
        $line5 = new Line('line5');
        $table2->addLine($line5);
        $line6 = new Line('line6');
        $table2->addLine($line6);

        $table2->setCell($line1, $col1, new Cell(rand(0,100)));
        $table2->setCell($line1, $col2, new Cell(rand(0,100)));
        $table2->setCell($line1, $col3, new Cell(rand(0,100)));
        $table2->setCell($line1, $col4, new Cell(rand(0,100)));
        $table2->setCell($line2, $col1, new Cell(rand(0,100)));
        $table2->setCell($line2, $col2, new Cell(rand(0,100)));
        $table2->setCell($line2, $col3, new Cell(rand(0,100)));
        $table2->setCell($line2, $col4, new Cell(rand(0,100)));
        $table2->setCell($line3, $col1, new Cell(rand(0,100)));
        $table2->setCell($line3, $col2, new Cell(rand(0,100)));
        $table2->setCell($line3, $col3, new Cell(rand(0,100)));
        $table2->setCell($line3, $col4, new Cell(rand(0,100)));
        $table2->setCell($line4, $col1, new Cell(rand(0,100)));
        $table2->setCell($line4, $col2, new Cell(rand(0,100)));
        $table2->setCell($line4, $col3, new Cell(rand(0,100)));
        $table2->setCell($line4, $col4, new Cell(rand(0,100)));
        $table2->setCell($line5, $col1, new Cell(rand(0,100)));
        $table2->setCell($line5, $col2, new Cell(rand(0,100)));
        $table2->setCell($line5, $col3, new Cell(rand(0,100)));
        $table2->setCell($line5, $col4, new Cell(rand(0,100)));
        $table2->setCell($line6, $col1, new Cell(rand(0,100)));
        $table2->setCell($line6, $col2, new Cell(rand(0,100)));
        $table2->setCell($line6, $col3, new Cell(rand(0,100)));
        $table2->setCell($line6, $col4, new Cell(rand(0,100)));

        $exporter->export($document, __DIR__ . '/../test.xslx');
    }
}
