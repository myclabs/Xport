<?php

namespace Xport\Parser;

use MetaModel\MetaModel;
use Xport\Parser\ParsingException;

/**
 * "foreach" expression executor
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ForEachExecutor
{
    /**
     * @var ForEachParser
     */
    private $forEachParser;

    public function __construct()
    {
        $this->forEachParser = new ForEachParser();
    }

    /**
     * Executes a foreach expression.
     *
     * The expression has the form: 'foo as bar', where foo is an array and bar the value.
     *
     * @param string $expression foreach expression
     * @param scope $scope
     *
     * @throws ParsingException
     * @return Scope[]
     */
    public function execute($expression, Scope $scope)
    {
        $metaModel = new MetaModel();
        $metaModel->addContainer($scope);

        $subScopes = [];

        $parseResult = $this->forEachParser->parse($expression);

        $resultArray = $this->parseFunction($parseResult['array']);
        if (is_array($resultArray)) {
            $function = $scope->getFunction($resultArray['functionName']);
            $array = $function($metaModel->run($resultArray['parameter']));
        } else {
            $array = $metaModel->run($parseResult['array']);
        }

        foreach ($array as $key => $value) {
            // New sub-scope
            $subScope = new Scope($scope);

            $resultValue = $this->parseFunction($parseResult['value']);
            if (is_array($resultValue)) {
                $function = $scope->getFunction($resultValue['functionName']);
                $subScope->bind($resultValue['parameter'], $function($value));
            } else {
                $subScope->bind($parseResult['value'], $value);
            }

            if (isset($parseResult['key'])) {
                $resultKey = $this->parseFunction($parseResult['key']);
                if (is_array($resultKey)) {
                    $function = $scope->getFunction($resultKey['functionName']);
                    $subScope->bind($resultKey['parameter'], $function($key));
                } else {
                    $subScope->bind($parseResult['key'], $key);
                }
            }

            $subScopes[] = $subScope;
        }

        return $subScopes;
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     * @todo Move into ForEachParser
     */
    private function parseFunction($str)
    {
        $result = preg_match('/^\s*([[:alnum:]]+)\(([[:alnum:]\.\[\]]+)\)\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'functionName' => $matches[1],
            'parameter'    => $matches[2],
        ];
    }
}
