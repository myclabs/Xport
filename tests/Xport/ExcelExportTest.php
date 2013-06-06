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
        $cell1->inputSets = [];
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

        var_dump($result);
    }

}
