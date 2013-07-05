<?php

namespace Xport\SpreadsheetModel\Parser;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Xport\SpreadsheetModel\Parser\ParsingException;

/**
 * "foreach" parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ForEachParser
{
    /**
     * @var String
     */
    private $partRegEx;
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    public function __construct()
    {
        $this->partRegEx = '([[:alnum:]]+\([[:alnum:]\.\[\]]+\)|[[:alnum:]\.\[\]]+)';
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Parse a foreach expression.
     *
     * The expression has the form: 'foo as bar', where foo is an array and bar the value.
     *
     * @param string $str foreach expression
     * @param scope $scope
     *
     * @throws ParsingException
     * @return Scope[]
     */
    public function parse($str, $scope)
    {
        $result = $this->parseWithKey($str);

        if (is_array($result)) {
            return $this->getSubScopes($result, $scope);
        }

        $result = $this->parseWithoutKey($str);

        if (is_array($result)) {
            return $this->getSubScopes($result, $scope);
        }

        throw new ParsingException("Error while parsing '$str', should be in the form 'array as value' or 'array as key => value'");
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     */
    public function parseWithKey($str)
    {
        $result = preg_match('/^\s*'.$this->partRegEx.'\s*as\s*'.$this->partRegEx.'\s*=>\s*'.$this->partRegEx.'\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'key'   => $matches[2],
            'value' => $matches[3],
        ];
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     */
    public function parseWithoutKey($str)
    {
        $result = preg_match('/^\s*'.$this->partRegEx.'\s*as\s*'.$this->partRegEx.'\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'array' => $matches[1],
            'value' => $matches[2],
        ];
    }

    /**
     * @param string $str
     * @return array|null Keys are 'array' and 'value'
     */
    public function parseFunction($str)
    {
        $result = preg_match('/^\s*([[:alnum:]]+)\(([[:alnum:]\.\[\]]+)\)\s*$/', $str, $matches);

        if ($result !== 1) {
            return null;
        }

        return [
            'functionName' => $matches[1],
            'parameter' => $matches[2],
        ];
    }

    /**
     * @param array $result
     * @param Scope $scope
     * @return Scope[]
     */
    public function getSubScopes($result, Scope $scope)
    {
        $subScopes = [];

        $resultArray = $this->parseFunction($result['array']);

        if (is_array($resultArray)) {
            $function = $scope->getFunction($resultArray['functionName']);
            $array = $function($this->propertyAccessor->getValue($scope, $resultArray['parameter']));
        } else {
            $array = $this->propertyAccessor->getValue($scope, $result['array']);
        }

        foreach ($array as $key => $value) {
            // New sub-scope
            $subScope = new Scope($scope);

            $resultValue = $this->parseFunction($result['value']);

            if (is_array($resultValue)) {
                $function = $scope->getFunction($resultValue['functionName']);
                $subScope->bind($resultValue['parameter'], $function($value));
            } else {
                $subScope->bind($result['value'], $value);
            }

            if (isset($result['key'])) {
                $resultKey = $this->parseFunction($result['key']);

                if (is_array($resultKey)) {
                    $function = $scope->getFunction($resultKey['functionName']);
                    $subScope->bind($resultKey['parameter'], $function($key));
                } else {
                    $subScope->bind($result['key'], $key);
                }
            }

            $subScopes[] = $subScope;
        }

        return $subScopes;
    }
}
