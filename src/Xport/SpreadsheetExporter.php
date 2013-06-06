<?php

namespace Xport;

use Xport\SpreadsheetModel\SpreadsheetModel;

/**
 * Exports an Spreadsheet model to a file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class SpreadsheetExporter
{
    /**
     * Exports an Spreadsheet model to a file.
     *
     * @param SpreadsheetModel $model
     * @param string           $targetFile
     */
    public abstract function export(SpreadsheetModel $model, $targetFile);
}
