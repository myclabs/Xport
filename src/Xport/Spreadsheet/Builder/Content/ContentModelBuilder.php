<?php

namespace Xport\Spreadsheet\Builder\Content;

use Xport\Parser\Scope;
use Xport\Spreadsheet\Model\Sheet;

/**
 * Content model builder
 *
 * @author valentin-mcs <valentin.claras@myc-sense.com>
 */
interface ContentModelBuilder
{
    /**
     * @param Sheet $sheet
     * @param $yamlContent
     * @param \Xport\Parser\Scope $scope
     */
    public function build(Sheet $sheet, $yamlContent, Scope $scope);

}
