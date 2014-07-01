<?php

namespace Xport\Spreadsheet\Builder;

use Xport\MappingReader\MappingReader;
use Xport\Parser\Scope;
use Xport\Spreadsheet\Model\Sheet;
use Xport\Spreadsheet\Reader\Reader;

/**
 * Hydrates objects from a spreadsheet model
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class HydratorFromSpreadsheet
{
    /**
     * @var Scope
     */
    private $scope;

    public function __construct()
    {
        $this->scope = new Scope();
    }

    /**
     * @param Reader        $reader
     * @param MappingReader $mappingReader
     */
    public function hydrate(Reader $reader, MappingReader $mappingReader)
    {
        // Read the document
        $mapping = $mappingReader->getMapping();
        foreach ($mapping['sheets'] as $sheetMapping) {
            // Foreach block
            if (isset($sheetMapping['foreach'])) {
                // TODO
                continue;
            }

            $this->readSheet($sheetMapping, $reader);
        }

        $document = $reader->getDocument();

        $sheets = $document->getSheets();
        foreach ($mapping['sheets'] as $i => $sheetMapping) {
            // Foreach block
            if (isset($sheetMapping['foreach'])) {
                // TODO
                continue;
            }

            $this->hydrateFromSheet($sheets[$i], $sheetMapping);
        }
    }

    private function readSheet($sheetMapping, Reader $reader)
    {
        $reader->openNextSheet();

        foreach ($sheetMapping['content'] as $table) {
            // Foreach block
            if (isset($sheetMapping['foreach'])) {
                // TODO
                continue;
            }

            $hasLabel = isset($table['label']);
            $hasColumnHeaders = isset($table['columns']);

            $reader->readNextTable($hasLabel, $hasColumnHeaders);
        }
    }

    private function hydrateFromSheet(Sheet $sheet, $sheetMapping)
    {
        foreach ($sheetMapping['content'] as $table) {
            // Foreach block
            if (isset($sheetMapping['foreach'])) {
                // TODO
                continue;
            }

            $hasLabel = isset($table['label']);
            $hasColumnHeaders = isset($table['columns']);
        }
    }

    /**
     * Bind a variable to a name.
     *
     * @param string $name
     * @param mixed  $variable
     */
    public function bind($name, &$variable)
    {
        $this->scope->bind($name, $variable);
    }
}
