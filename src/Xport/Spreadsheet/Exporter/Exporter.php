<?php

namespace Xport\Spreadsheet\Exporter;

use Xport\Spreadsheet\Model\Document;

/**
 * Exports a Spreadsheet model to a file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Exporter
{
    /**
     * Exports a Spreadsheet model to a file.
     *
     * @param Document $model Xport model
     * @param string   $targetFile
     */
    public function export(Document $model, $targetFile);
}
