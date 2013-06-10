<?php

namespace Xport\MappingReader;

use Symfony\Component\Yaml\Yaml;
use Xport\SpreadsheetModel\Parser\ParsingException;

/**
 * YAML file reader
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class YamlMappingReader extends MappingReader
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @param string $fileName
     * @throws \InvalidArgumentException File not found or not readable
     */
    public function __construct($fileName)
    {
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new \InvalidArgumentException("The file '$fileName' has not been found or is not readable");
        }
        $this->fileName = $fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping()
    {
        try {
            return Yaml::parse($this->fileName);
        } catch (\Exception $e) {
            throw new ParsingException($e->getMessage());
        }
    }
}
