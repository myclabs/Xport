<?php

namespace XportTest;

use Xport\MappingReader\YamlMappingReader;
use Xport\SpreadsheetModel\SpreadsheetModel;
use Xport\SpreadsheetModel\Sheet;
use Xport\SpreadsheetModelBuilder;

class SpreadsheetModelBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyRoot()
    {
        $mapping = [];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof SpreadsheetModel);
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
        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof SpreadsheetModel);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertEquals('Foo', $sheet->getLabel());
        $this->assertCount(0, $sheet->getTables());
    }

    public function testSheetForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'foreach' => 'foo as i => bar',
                    'label'   => '{{ i }} - Foo',
                ],
            ],
        ];

        $mappingReader = $this->getMockForAbstractClass('Xport\MappingReader\MappingReader');
        $mappingReader->expects($this->once())->method('getMapping')->will($this->returnValue($mapping));

        $modelBuilder = new SpreadsheetModelBuilder();
        $modelBuilder->bind('foo', ['test', 'test']);
        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof SpreadsheetModel);
        $this->assertCount(2, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertEquals('0 - Foo', $sheet->getLabel());
        $this->assertCount(0, $sheet->getTables());
    }

    public function testTableNormal()
    {
        $mapping = [
            'sheets' => [
                [
                    'tables' => [
                        [
                            'lines'   => [
                                'foreach' => 'foo as i => bar',
                            ],
                            'columns' => [
                                [
                                    'label'       => 'Col1',
                                    'cellContent' => '{{ i }}',
                                ],
                                [
                                    'label'       => 'Col2',
                                    'cellContent' => '{{ bar }}',
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
        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof SpreadsheetModel);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertCount(1, $sheet->getTables());

        $table = $sheet->getTables()[0];
        $this->assertCount(2, $table->getLines());

        // Columns
        $this->assertEquals('Col1', $table->getColumns()[0]->getLabel());
        $this->assertEquals('Col2', $table->getColumns()[1]->getLabel());

        // Cells
        $this->assertEquals('0', $table->getCell($table->getLines()[0], $table->getColumns()[0])->getContent());
        $this->assertEquals('1', $table->getCell($table->getLines()[1], $table->getColumns()[0])->getContent());
        $this->assertEquals('test1', $table->getCell($table->getLines()[0], $table->getColumns()[1])->getContent());
        $this->assertEquals('test2', $table->getCell($table->getLines()[1], $table->getColumns()[1])->getContent());
    }

    public function testTableForEach()
    {
        $mapping = [
            'sheets' => [
                [
                    'tables' => [
                        [
                            'foreach' => 'list as item',
                            'lines'   => [
                                'foreach' => 'foo as i => bar',
                            ],
                            'columns' => [
                                [
                                    'label'       => 'Col1',
                                    'cellContent' => '{{ i }}',
                                ],
                                [
                                    'label'       => 'Col2',
                                    'cellContent' => '{{ bar }}',
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
        /** @var SpreadsheetModel $result */
        $result = $modelBuilder->build($mappingReader);

        $this->assertTrue($result instanceof SpreadsheetModel);
        $this->assertCount(1, $result->getSheets());

        $sheet = $result->getSheets()[0];
        $this->assertCount(2, $sheet->getTables());

        // Same table
        $this->assertEquals($sheet->getTables()[0], $sheet->getTables()[1]);
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testTableWithoutLines()
    {
        $mapping = [
            'sheets' => [
                [
                    'tables' => [
                        [
                            'columns' => [
                                [
                                    'label'       => 'Col1',
                                    'cellContent' => '{{ i }}',
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
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testTableWithoutColumns()
    {
        $mapping = [
            'sheets' => [
                [
                    'tables' => [
                        [
                            'lines'   => [
                                'foreach' => 'foo as i => bar',
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
