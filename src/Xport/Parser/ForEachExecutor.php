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

        if (is_array($parseResult['array'])) {
            $functionName = $scope->getFunction($parseResult['array']['functionName']);
            $array = call_user_func_array(
                $functionName,
                array_map(
                    function ($parameter) use ($metaModel) { return $metaModel->run($parameter); },
                    $parseResult['array']['parameters']
                )
            );
        } else {
            $array = $metaModel->run($parseResult['array']);
        }

        foreach ($array as $key => $value) {
            // New sub-scope
            $subScope = new Scope($scope);

            $subScope->bind($parseResult['value'], $value);

            if (isset($parseResult['key'])) {
                $subScope->bind($parseResult['key'], $key);
            }

            $subScopes[] = $subScope;
        }

        return $subScopes;
    }
}
