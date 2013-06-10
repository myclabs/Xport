<?php

namespace XportTest;

use Xport\MappingReader\YamlMappingReader;
use Xport\SpreadsheetModel\SpreadsheetModel;
use Xport\SpreadsheetModel\Sheet;
use Xport\SpreadsheetModelBuilder;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testSpreadsheetModelBuilder()
    {
        $modelBuilder = new SpreadsheetModelBuilder();

        $cells = [];

        $cell1 = new \stdClass();
        $cell1->name = 'Foo';
        $inputSet11 = new \stdClass();
        $input111 = new \stdClass();
        $input111->value = 10;
        $input111->uncertainty = 0.15;
        $inputSet11->inputs = [$input111];
        $cell1->inputSets = [$inputSet11];
        $cells[] = $cell1;

        $cell2 = new \stdClass();
        $cell2->name = 'Bar';
        $cell2->inputSets = [];
        $cells[] = $cell2;

        $modelBuilder->bind('cells', $cells);

        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build(new YamlMappingReader(__DIR__ . '/Fixtures/mapping.yml'));

        $this->assertTrue($result instanceof SpreadsheetModel);
        $this->assertCount(2, $result->getSheets());

        foreach ($result->getSheets() as $sheet) {
            $this->assertTrue($sheet instanceof Sheet);
        }

        $sheet = $result->getSheets()[0];
        $this->assertEquals('1 - Foo', $sheet->getLabel());
        $this->assertCount(1, $sheet->getTables());

        $table = $sheet->getTables()[0];
        $this->assertCount(1, $table->getLines());
        $this->assertCount(2, $table->getColumns());
        $this->assertCount(2, $table->getCells());
    }
}
