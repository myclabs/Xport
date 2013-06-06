<?php

namespace Xport;

use Xport\Excel\File;
use Xport\Excel\Sheet;

class ExcelExportTest extends \PHPUnit_Framework_TestCase
{

    public function testExport()
    {
        $exporter = new ExcelExport();

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

        var_dump($result);
    }

}
