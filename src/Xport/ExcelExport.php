<?php

namespace Xport;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Yaml\Parser;
use Xport\Excel\File;
use Xport\Excel\Sheet;

/**
 * Excel export
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ExcelExport
{

    public function export($mappingFile, $dataSource)
    {
        $yaml = file_get_contents($mappingFile);

        $yamlReader = new Parser();
        $yamlStructure = $yamlReader->parse($yaml);

        $file = new File();
        $this->parseItem($file, $yamlStructure, $dataSource);

        return $file;
    }

    private function parseItem($excelItem, $yamlItem, $dataSource)
    {
        foreach ($yamlItem as $key => $yamlSubItem) {
            // forEach
            if (strpos($key, 'forEach(') !== false) {
                $result = preg_match('/^forEach\(([^\)]+)\)$/', $key, $matches);
                if ($result !== 1 || !isset($matches[1])) {
                    throw new \Exception("Parse error on $key");
                }

                $forEachPropertyPath = $matches[1];

                $this->processForEach($excelItem, $yamlSubItem, $dataSource, $forEachPropertyPath);
            }
            // Sheet
            if ($key === 'sheet') {
                if ($excelItem instanceof File) {
                    $sheet = new Sheet();
                    $excelItem->addSheet($sheet);

                    $this->parseItem($sheet, $yamlSubItem, $dataSource);
                } else {
                    throw new \Exception("'sheet' must be at the root of the Excel file");
                }
            }
        }
    }

    private function processForEach($excelItem, $yamlItem, $dataSource, $propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $iterator = $accessor->getValue($dataSource, $propertyPath);

        foreach ($iterator as $key => $newDataSource) {
            $this->parseItem($excelItem, $yamlItem, $newDataSource);
        }
    }

}
