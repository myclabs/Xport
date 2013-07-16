<?php

namespace XportTest;

use Xport\MappingReader\YamlMappingReader;
use Xport\Spreadsheet\Model\Document;
use Xport\Spreadsheet\Builder\SpreadsheetModelBuilder;

class SpreadsheetModelBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyRoot()
    {
        $mapping = [];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(0, $result->getSheets());
    }

    public function testSheetNormal()
    {
        $mapping = [
            'sheets' => [
                [
                    'label' => 'Foo',
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(1, $result->getSheets());

        $sheet0 = $result->getSheets()[0];
        $this->assertEquals('Foo', $sheet0->getLabel());
        $this->assertCount(0, $sheet0->getTables());
    }

    public function testSheetForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'foreach' => 'foo as i => bar',
                    'do' => [
                        [
                            'label'   => '{{ i }} - Foo'
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('foo', ['test', 'test']);
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(2, $result->getSheets());

        $sheet0 = $result->getSheets()[0];
        $this->assertEquals('0 - Foo', $sheet0->getLabel());
        $this->assertCount(0, $sheet0->getTables());

        $sheet1 = $result->getSheets()[1];
        $this->assertEquals('1 - Foo', $sheet1->getLabel());
        $this->assertCount(0, $sheet1->getTables());
    }

    public function testSheetForEachForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'foreach' => 'foo as i => bar',
                    'do' => [
                        [
                            'foreach' => 'foo as j => bim',
                            'do' => [
                                [
                                    'label'   => '{{ i }} - {{ j }} - Foo'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('foo', ['test', 'test']);
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(4, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertEquals('0 - 0 - Foo', $sheet->getLabel());
        $this->assertCount(0, $sheet->getTables());

        $sheet1 = $result->getSheets()[1];
        $this->assertEquals('0 - 1 - Foo', $sheet1->getLabel());
        $this->assertCount(0, $sheet1->getTables());

        $sheet2 = $result->getSheets()[2];
        $this->assertEquals('1 - 0 - Foo', $sheet2->getLabel());
        $this->assertCount(0, $sheet2->getTables());

        $sheet3 = $result->getSheets()[3];
        $this->assertEquals('1 - 1 - Foo', $sheet3->getLabel());
        $this->assertCount(0, $sheet3->getTables());
    }

    public function testContentNormal()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'type' => 'VerticalTable',
                            'columns' => [
                                'Col1',
                                'Col2'
                            ],
                            'lines'   => [
                                [
                                    'foreach' => 'foo as i => bar',
                                    'do' => [
                                        [
                                            'cells' => [
                                                '{{ i }}',
                                                '{{ bar }}',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('foo', ['test1', 'test2']);
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertCount(1, $sheet->getTables());

        $table = $sheet->getTables()[0];
        $this->assertCount(2, $table->getLines());

        // Columns
        $this->assertCount(2, $table->getColumns());
        $this->assertEquals('Col1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('Col2', $table->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(4, $table->getCells());
        $this->assertEquals('0', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('1', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('test1', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('test2', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());
    }

    public function testContentForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'foreach' => 'list as item',
                            'do' => [
                                [
                                    'type' => 'VerticalTable',
                                    'columns' => [
                                        'Col1',
                                        'Col2',
                                    ],
                                    'lines'   => [
                                        [
                                            'foreach' => 'foo as i => bar',
                                            'do' => [
                                                [
                                                    'cells' => [
                                                        '{{ i }}',
                                                        '{{ bar }}',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('list', ['test1', 'test2']);
        $modelBuilder->bind('foo', ['test1', 'test2']);
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertCount(2, $sheet->getTables());

        // Table 0
        $table = $sheet->getTables()[0];
        $this->assertCount(2, $table->getLines());

        // Columns
        $this->assertCount(2, $table->getColumns());
        $this->assertEquals('Col1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('Col2', $table->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(4, $table->getCells());
        $this->assertEquals('0', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('1', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('test1', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('test2', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());

        // Same table
        $this->assertEquals($sheet->getTables()[0], $sheet->getTables()[1]);
    }

    public function testContentForEachForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'type' => 'VerticalTable',
                            'columns' => [
                                'Col1',
                                'Col2',
                            ],
                            'lines'   => [
                                [
                                    'foreach' => 'foo as i => bar',
                                    'do' => [
                                        [
                                            'cells' => [
                                                '{{ i }}',
                                                '{{ bar }}',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'foreach' => 'list as item',
                            'do' => [
                                [
                                    'type' => 'VerticalTable',
                                    'columns' => [
                                        'Col1',
                                        'Col2',
                                    ],
                                    'lines'   => [
                                        [
                                            'foreach' => 'foo as i => bar',
                                            'do' => [
                                                [
                                                    'cells' => [
                                                        '{{ i }}',
                                                        '{{ bar }}',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('list', ['test1', 'test2']);
        $modelBuilder->bind('foo', ['test1', 'test2']);
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertCount(3, $sheet->getTables());

        // Table 0
        $table = $sheet->getTables()[0];
        $this->assertCount(2, $table->getLines());

        // Columns
        $this->assertCount(2, $table->getColumns());
        $this->assertEquals('Col1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('Col2', $table->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(4, $table->getCells());
        $this->assertEquals('0', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('1', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('test1', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('test2', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());

        // Same table
        $this->assertEquals($sheet->getTables()[0], $sheet->getTables()[1]);
        $this->assertEquals($sheet->getTables()[0], $sheet->getTables()[2]);
    }

    public function testComplexForeachStructure()
    {
        /** @var Document $result */
        $mappingReader = new YamlMappingReader(__DIR__ . '/../Fixtures/maximalStructure.yml');


        $modelBuilder = new SpreadsheetModelBuilder();

        $simpleItem1 = new \stdClass();
        $simpleItem1->label = 'Test 1';
        $simpleItem1->value = 15;
        $simpleItem2= new \stdClass();
        $simpleItem2->label = 'Test 2';
        $simpleItem2->value = 28;
        $simpleItem3= new \stdClass();
        $simpleItem3->label = 'Test 3';
        $simpleItem3->value = 34;
        $simpleItem4= new \stdClass();
        $simpleItem4->label = 'Test 4';
        $simpleItem4->value = 7;
        $modelBuilder->bind('listItemsSheet1', [$simpleItem1, $simpleItem2]);
        $modelBuilder->bind('listSheets2', ['Sheet 2', 'Sheet 3', 'Sheet 4']);
        $modelBuilder->bind('listItemsSheet2', [$simpleItem1, $simpleItem2, $simpleItem3, $simpleItem4]);
        $complexItem = new \stdClass();
        $category1 = new \stdClass();
        $category1->label = 'Category 1';
        $category2 = new \stdClass();
        $category2->label = 'Category 2';
        $category3 = new \stdClass();
        $category3->label = 'Category 3';
        $complexItem->categories = [1 => $category1, 2 => $category2, 3 => $category3];
        $valueItem1 = new \stdClass();
        $valueItem1->label = 'Value 1';
        $valueItem1->values[1] = 40;
        $valueItem1->values[2] = 35;
        $valueItem1->values[3] = 25;
        $valueItem2 = new \stdClass();
        $valueItem2->label = 'Value 2';
        $valueItem2->values[1] = 10;
        $valueItem2->values[2] = 20;
        $valueItem2->values[3] = 70;
        $valueItem3 = new \stdClass();
        $valueItem3->label = 'Value 3';
        $valueItem3->values[1] = 45;
        $valueItem3->values[2] = 5;
        $valueItem3->values[3] = 50;
        $valueItem4 = new \stdClass();
        $valueItem4->label = 'Value 4';
        $valueItem4->values[1] = 30;
        $valueItem4->values[2] = 25;
        $valueItem4->values[3] = 45;
        $valueItem5 = new \stdClass();
        $valueItem5->label = 'Value 5';
        $valueItem5->values[1] = 65;
        $valueItem5->values[2] = 25;
        $valueItem5->values[3] = 10;
        $complexItem->values = [$valueItem1, $valueItem2, $valueItem3, $valueItem4, $valueItem5];
        $modelBuilder->bind('listItemsSheet3', $complexItem);
        $modelBuilder->bindFunction('categoryPercent', function(\stdClass $item, $indexCategory) {
                return (($item->values[$indexCategory] / array_sum($item->values)) * 100);
            });

        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof Document);
        $this->assertCount(5, $result->getSheets());

        // Sheet 0 ----------------------------------------------------|
        $sheet0 = $result->getSheets()[0];
        $this->assertEquals('First', $sheet0->getLabel());
        $this->assertCount(1, $sheet0->getTables());

        // Table
        $table = $sheet0->getTables()[0];
        $this->assertCount(3, $table->getLines());

        // Columns
        $this->assertCount(2, $table->getColumns());
        $this->assertEquals('F 1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('F 2', $table->getColumns()[1]->getLabel());

        // Cells
        $this->assertEquals('Test 1', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('Test 2', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('15', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('28', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());

        // Sheet 1 ----------------------------------------------------|
        $sheet1 = $result->getSheets()[1];
        $this->assertEquals('Sheet 2 (0)', $sheet1->getLabel());
        $this->assertCount(2, $sheet1->getTables());

        // Table 0
        $table0 = $sheet1->getTables()[0];
        $this->assertCount(4, $table0->getLines());

        // Columns
        $this->assertCount(2, $table0->getColumns());
        $this->assertEquals('S2-0 1', $table0->getColumns()[0]->getLabel());
        $this->assertEquals('S2-0 2', $table0->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(8, $table0->getCells());
        $this->assertEquals('Test 1', $table0->getCell($table0->getLines()[0], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 2', $table0->getCell($table0->getLines()[1], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 3', $table0->getCell($table0->getLines()[2], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 4', $table0->getCell($table0->getLines()[3], $table0->getColumns()[0])->getContent());
        $this->assertEquals('15', $table0->getCell($table0->getLines()[0], $table0->getColumns()[1])->getContent());
        $this->assertEquals('28', $table0->getCell($table0->getLines()[1], $table0->getColumns()[1])->getContent());
        $this->assertEquals('34', $table0->getCell($table0->getLines()[2], $table0->getColumns()[1])->getContent());
        $this->assertEquals('7', $table0->getCell($table0->getLines()[3], $table0->getColumns()[1])->getContent());

        // Table 1
        $table1 = $sheet1->getTables()[1];
        $this->assertCount(1, $table1->getLines());
        $this->assertCount(0, $table1->getColumns());
        $this->assertCount(0, $table1->getCells());

        // Sheet 2 ----------------------------------------------------|
        $sheet2 = $result->getSheets()[2];
        $this->assertEquals('Sheet 3 (1)', $sheet2->getLabel());
        $this->assertCount(2, $sheet2->getTables());

        // Table 0
        $table0 = $sheet2->getTables()[0];
        $this->assertCount(4, $table0->getLines());

        // Columns
        $this->assertCount(2, $table0->getColumns());
        $this->assertEquals('S2-1 1', $table0->getColumns()[0]->getLabel());
        $this->assertEquals('S2-1 2', $table0->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(8, $table0->getCells());
        $this->assertEquals('Test 1', $table0->getCell($table0->getLines()[0], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 2', $table0->getCell($table0->getLines()[1], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 3', $table0->getCell($table0->getLines()[2], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 4', $table0->getCell($table0->getLines()[3], $table0->getColumns()[0])->getContent());
        $this->assertEquals('15', $table0->getCell($table0->getLines()[0], $table0->getColumns()[1])->getContent());
        $this->assertEquals('28', $table0->getCell($table0->getLines()[1], $table0->getColumns()[1])->getContent());
        $this->assertEquals('34', $table0->getCell($table0->getLines()[2], $table0->getColumns()[1])->getContent());
        $this->assertEquals('7', $table0->getCell($table0->getLines()[3], $table0->getColumns()[1])->getContent());

        // Table 1
        $table1 = $sheet2->getTables()[1];
        $this->assertCount(1, $table1->getLines());
        $this->assertCount(0, $table1->getColumns());
        $this->assertCount(0, $table1->getCells());

        // Sheet 3 ----------------------------------------------------|
        $sheet3 = $result->getSheets()[3];
        $this->assertEquals('Sheet 4 (2)', $sheet3->getLabel());
        $this->assertCount(2, $sheet3->getTables());

        // Table 0
        $table0 = $sheet3->getTables()[0];
        $this->assertCount(4, $table0->getLines());

        // Columns
        $this->assertCount(2, $table0->getColumns());
        $this->assertEquals('S2-2 1', $table0->getColumns()[0]->getLabel());
        $this->assertEquals('S2-2 2', $table0->getColumns()[1]->getLabel());

        // Cells
        $this->assertCount(8, $table0->getCells());
        $this->assertEquals('Test 1', $table0->getCell($table0->getLines()[0], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 2', $table0->getCell($table0->getLines()[1], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 3', $table0->getCell($table0->getLines()[2], $table0->getColumns()[0])->getContent());
        $this->assertEquals('Test 4', $table0->getCell($table0->getLines()[3], $table0->getColumns()[0])->getContent());
        $this->assertEquals('15', $table0->getCell($table0->getLines()[0], $table0->getColumns()[1])->getContent());
        $this->assertEquals('28', $table0->getCell($table0->getLines()[1], $table0->getColumns()[1])->getContent());
        $this->assertEquals('34', $table0->getCell($table0->getLines()[2], $table0->getColumns()[1])->getContent());
        $this->assertEquals('7', $table0->getCell($table0->getLines()[3], $table0->getColumns()[1])->getContent());

        // Table 1
        $table1 = $sheet3->getTables()[1];
        $this->assertCount(1, $table1->getLines());
        $this->assertCount(0, $table1->getColumns());
        $this->assertCount(0, $table1->getCells());

        // Sheet 4 ----------------------------------------------------|
        $sheet4 = $result->getSheets()[4];
        $this->assertEquals('Inter', $sheet4->getLabel());
        $this->assertCount(1, $sheet4->getTables());

        // Table
        $table = $sheet4->getTables()[0];
        $this->assertCount(5, $table->getLines());

        // Columns
        $this->assertCount(10, $table->getColumns());
        $this->assertEquals('I 1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('', $table->getColumns()[1]->getLabel());
        $this->assertEquals('Category 1', $table->getColumns()[2]->getLabel());
        $this->assertEquals('ratio', $table->getColumns()[3]->getLabel());
        $this->assertEquals('', $table->getColumns()[4]->getLabel());
        $this->assertEquals('Category 2', $table->getColumns()[5]->getLabel());
        $this->assertEquals('ratio', $table->getColumns()[6]->getLabel());
        $this->assertEquals('', $table->getColumns()[7]->getLabel());
        $this->assertEquals('Category 3', $table->getColumns()[8]->getLabel());
        $this->assertEquals('ratio', $table->getColumns()[9]->getLabel());

        // Cells
        $this->assertCount(50, $table->getCells());
        $this->assertEquals('Value 1', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('Value 2', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('Value 3', $table->getCell($table->getLines()[2], $table->getColumns()[0])->getContent());
        $this->assertEquals('Value 4', $table->getCell($table->getLines()[3], $table->getColumns()[0])->getContent());
        $this->assertEquals('Value 5', $table->getCell($table->getLines()[4], $table->getColumns()[0])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[2], $table->getColumns()[1])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[3], $table->getColumns()[1])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[4], $table->getColumns()[1])->getContent());
        $this->assertEquals('40', $table->getCell($table->getLines()[0], $table->getColumns()[2])->getContent());
        $this->assertEquals('10', $table->getCell($table->getLines()[1], $table->getColumns()[2])->getContent());
        $this->assertEquals('45', $table->getCell($table->getLines()[2], $table->getColumns()[2])->getContent());
        $this->assertEquals('30', $table->getCell($table->getLines()[3], $table->getColumns()[2])->getContent());
        $this->assertEquals('65', $table->getCell($table->getLines()[4], $table->getColumns()[2])->getContent());
        $this->assertEquals('40', $table->getCell($table->getLines()[0], $table->getColumns()[3])->getContent());
        $this->assertEquals('10', $table->getCell($table->getLines()[1], $table->getColumns()[3])->getContent());
        $this->assertEquals('45', $table->getCell($table->getLines()[2], $table->getColumns()[3])->getContent());
        $this->assertEquals('30', $table->getCell($table->getLines()[3], $table->getColumns()[3])->getContent());
        $this->assertEquals('65', $table->getCell($table->getLines()[4], $table->getColumns()[3])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[0], $table->getColumns()[4])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[1], $table->getColumns()[4])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[2], $table->getColumns()[4])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[3], $table->getColumns()[4])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[4], $table->getColumns()[4])->getContent());
        $this->assertEquals('35', $table->getCell($table->getLines()[0], $table->getColumns()[5])->getContent());
        $this->assertEquals('20', $table->getCell($table->getLines()[1], $table->getColumns()[5])->getContent());
        $this->assertEquals('5', $table->getCell($table->getLines()[2], $table->getColumns()[5])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[3], $table->getColumns()[5])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[4], $table->getColumns()[5])->getContent());
        $this->assertEquals('35', $table->getCell($table->getLines()[0], $table->getColumns()[6])->getContent());
        $this->assertEquals('20', $table->getCell($table->getLines()[1], $table->getColumns()[6])->getContent());
        $this->assertEquals('5', $table->getCell($table->getLines()[2], $table->getColumns()[6])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[3], $table->getColumns()[6])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[4], $table->getColumns()[6])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[0], $table->getColumns()[7])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[1], $table->getColumns()[7])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[2], $table->getColumns()[7])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[3], $table->getColumns()[7])->getContent());
        $this->assertEquals('', $table->getCell($table->getLines()[4], $table->getColumns()[7])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[0], $table->getColumns()[8])->getContent());
        $this->assertEquals('70', $table->getCell($table->getLines()[1], $table->getColumns()[8])->getContent());
        $this->assertEquals('50', $table->getCell($table->getLines()[2], $table->getColumns()[8])->getContent());
        $this->assertEquals('45', $table->getCell($table->getLines()[3], $table->getColumns()[8])->getContent());
        $this->assertEquals('10', $table->getCell($table->getLines()[4], $table->getColumns()[8])->getContent());
        $this->assertEquals('25', $table->getCell($table->getLines()[0], $table->getColumns()[9])->getContent());
        $this->assertEquals('70', $table->getCell($table->getLines()[1], $table->getColumns()[9])->getContent());
        $this->assertEquals('50', $table->getCell($table->getLines()[2], $table->getColumns()[9])->getContent());
        $this->assertEquals('45', $table->getCell($table->getLines()[3], $table->getColumns()[9])->getContent());
        $this->assertEquals('10', $table->getCell($table->getLines()[4], $table->getColumns()[9])->getContent());
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     * @expectedExceptionMessage 'table' must contain 'type'
     */
    public function testTableWithoutType()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'columns' => [
                                'Col1',
                                'Col2',
                            ],
                            'lines'   => [
                                [
                                    'foreach' => 'foo as i => bar',
                                    'do' => [
                                        [
                                            'cells' => [
                                                '{{ i }}',
                                                '{{ bar }}',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->build($mappingReader);
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     * @expectedExceptionMessage 'content' of type 'VerticalTable' must contains 'columns'
     */
    public function testTableWithoutColumns()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'type' => 'VerticalTable',
                            'lines'   => [
                                [
                                    'foreach' => 'foo as i => bar',
                                    'do' => [
                                        [
                                            'cells' => [
                                                '{{ i }}',
                                                '{{ bar }}',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->build($mappingReader);
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     * @expectedExceptionMessage 'content' of type 'VerticalTable' must contains 'lines'
     */
    public function testTableWithoutLines()
    {
        $mapping = [
            'sheets' => [
                [
                    'content' => [
                        [
                            'type' => 'VerticalTable',
                            'columns' => [
                                'Col1',
                                'Col2',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->build($mappingReader);
    }
}
