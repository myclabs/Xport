<?php

namespace Xport\Spreadsheet\Builder\Content;

use Xport\Parser\Scope;
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
     * {@inheritdoc}
     */
    public function build(Sheet $sheet, $yamlContent, Scope $scope)
    {
        $table = new Table();
        $table->addLine(new Line());

        $sheet->addTable($table);
    }
}
