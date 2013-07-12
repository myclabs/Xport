<?php

namespace Xport\Spreadsheet\Builder;

use Xport\MappingReader\MappingReader;
use Xport\Parser\Scope;
use Xport\Parser\TwigExecutor;
use Xport\Parser\ParsingException;
use Xport\Spreadsheet\Builder\Content\ContentModelBuilder;
use Xport\Spreadsheet\Builder\Content\EmptyLineModelBuilder;
use Xport\Spreadsheet\Builder\Content\VerticalTableModelBuilder;
use Xport\Spreadsheet\Model\Document;
use Xport\Spreadsheet\Model\Sheet;

/**
 * Builds a Spreadsheet model
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class SpreadsheetModelBuilder extends ModelBuilder
{
    /**
     * @var Scope
     */
    private $scope;

    /**
     * @var ContentModelBuilder[]
     */
    private $contentModelBuilders;

    public function __construct()
    {
        parent::__construct();

        $this->scope = new Scope();

        $this->addContentModelBuilder('EmptyLine', new EmptyLineModelBuilder());
        $this->addContentModelBuilder('VerticalTable', new VerticalTableModelBuilder());
    }

    /**
     * @param string              $refContentType
     * @param ContentModelBuilder $contentModelBuilder
     */
    public function addContentModelBuilder($refContentType, ContentModelBuilder $contentModelBuilder)
    {
        $this->contentModelBuilders[$refContentType] = $contentModelBuilder;
    }

    /**
     * @param string $refContentType
     * @return ContentModelBuilder
     * @throws \InvalidArgumentException
     */
    public function getContentModelBuilder($refContentType)
    {
        if (!isset($this->contentModelBuilders[$refContentType]))
        {
            throw new \InvalidArgumentException("No 'ContentModelBuilder' found for ref '$refContentType'.");
        }

        return $this->contentModelBuilders[$refContentType];
    }

    /**
     * Bind a value to a name.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function bind($name, $value)
    {
        $this->scope->bind($name, $value);
    }

    /**
     * Bind a function to a name.
     *
     * @param string   $name
     * @param callable $callable
     */
    public function bindFunction($name, $callable)
    {
        $this->scope->bindFunction($name, $callable);
    }

    /**
     * Build a model.
     *
     * @param MappingReader $mappingReader
     * @throws ParsingException
     * @return Document
     */
    public function build(MappingReader $mappingReader)
    {
        // Init TwigExecutor with all user functions.
        $this->twigExecutor = new TwigExecutor($this->scope->getFunctions());

        $document = new Document();
        $this->parseRoot($document, $mappingReader->getMapping(), $this->scope);

        return $document;
    }

    protected function parseRoot(Document $document, $yamlRoot, Scope $scope)
    {
        if (!array_key_exists('sheets', $yamlRoot)) {
            return;
        }

        foreach ($yamlRoot['sheets'] as $yamlSheet) {
            $this->parseSheet($document, $yamlSheet, $scope);
        }
    }

    protected function parseSheet(Document $document, $yamlSheet, Scope $scope)
    {
        if (array_key_exists('foreach', $yamlSheet)) {
            $this->parseForeach($yamlSheet, $scope, [$this, 'parseSheet'], [$document]);
        } else {
            $this->createSheet($document, $yamlSheet, $scope);
        }
    }

    protected function createSheet(Document $document, $yamlSheet, Scope $scope)
    {
        $sheet = new Sheet();
        $document->addSheet($sheet);

        if (array_key_exists('label', $yamlSheet)) {
            $label = $this->twigExecutor->parse($yamlSheet['label'], $scope);
            $sheet->setLabel($label);
        }

        if (array_key_exists('content', $yamlSheet)) {
            foreach ($yamlSheet['content'] as $yamlContent) {
                $this->parseContent($sheet, $yamlContent, $scope);
            }
        }
    }

    protected function parseContent(Sheet $sheet, $yamlContent, Scope $scope)
    {
        if (array_key_exists('foreach', $yamlContent)) {
            $this->parseForeach($yamlContent, $scope, [$this, 'parseContent'], [$sheet]);
        } else {
            $this->createContent($sheet, $yamlContent, $scope);
        }
    }

    protected function createContent(Sheet $sheet, $yamlContent, Scope $scope)
    {
        if (!isset($yamlContent) || !array_key_exists('type', $yamlContent)) {
            throw new ParsingException("'table' must contain 'type'");
        }

        $this->getContentModelBuilder($yamlContent['type'])->build($sheet, $yamlContent, $scope);
    }

}
