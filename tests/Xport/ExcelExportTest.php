<?php

namespace Xport;

use Xport\Excel\File;
use Xport\Excel\Sheet;

class ExcelExportTest extends \PHPUnit_Framework_TestCase
{

    public function testExcelModel()
    {
        $exporter = new ExcelModelBuilder();

        $data = new \stdClass();
        $data->cells = [];

        $cell1 = new \stdClass();
        $inputSet11 = new \stdClass();
        $input111 = new \stdClass();
        $input111->value = 10;
        $input111->uncertainty = 0.15;
        $inputSet11->inputs = [$input111];
        $cell1->inputSets = [$inputSet11];
        $data->cells[] = $cell1;

        $cell2 = new \stdClass();
        $cell2->inputSets = [];
        $data->cells[] = $cell2;

        /** @var File $result */
        $result = $exporter->export(__DIR__ . '/Fixtures/excel.yml', $data);

        $this->assertTrue($result instanceof File);
        $this->assertCount(2, $result->getSheets());

        foreach ($result->getSheets() as $sheet) {
            $this->assertTrue($sheet instanceof Sheet);
        }

        $sheet = $result->getSheets()[0];
        $this->assertCount(1, $sheet->getTables());

        $table = $sheet->getTables()[0];
        $this->assertCount(1, $table->getLines());
        $this->assertCount(2, $table->getColumns());
        $this->assertCount(2, $table->getCells());
    }

    public function testExcelExport()
    {
        $exporter = new ExcelExport();

        $data = new \stdClass();
        $data->cells = [];

        $cell1 = new \stdClass();

        $inputSet11 = new \stdClass();
        $input111 = new \stdClass();
        $input111->value = 10;
        $input111->uncertainty = 0.15;
        $input112 = new \stdClass();
        $input112->value = 5;
        $input112->uncertainty = 0.43;
        $inputSet11->inputs = [$input111, $input112];

        $inputSet12 = new \stdClass();
        $input121 = new \stdClass();
        $input121->value = 20;
        $input121->uncertainty = 0.05;
        $inputSet12->inputs = [$input121];

        $cell1->inputSets = [$inputSet11, $inputSet12];
        $data->cells[] = $cell1;

        $cell2 = new \stdClass();
        $inputSet21 = new \stdClass();
        $inputSet21->inputs = [];
        $cell2->inputSets = [$inputSet21];
        $data->cells[] = $cell2;

        $exporter->export(__DIR__ . '/Fixtures/excel.yml', $data, __DIR__ . '/test.xslx');
    }

}
