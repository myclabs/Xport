<?php

namespace Xport\Spreadsheet\Builder\Content;

use Xport\Parser\Scope;
use Xport\Parser\ParsingException;
use Xport\Spreadsheet\Builder\ModelBuilder;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Model\Table;
use Xport\Spreadsheet\Model\Line;

/**
 * Builds a empty content model
 *
 * @author valentin-mcs <valentin.claras@myc-sense.fr>
 */
class EmptyLineModelBuilder extends ModelBuilder implements ContentModelBuilder
{
    /**
     * @param Sheet $sheet
     * @param $yamlContent
     * @param \Xport\Parser\Scope $scope
     * @throws ParsingException
     */
    public function build(Sheet $sheet, $yamlContent, Scope $scope)
    {
        // Table.
        $table = new Table();
        $sheet->addTable($table);

        // Line.
        $line = new Line();
        $table->addLine($line);
    }

}
