<?php

namespace Xport\Spreadsheet\Builder;

use Xport\Parser\Scope;
use Xport\Parser\TwigExecutor;
use Xport\Parser\ForEachExecutor;

/**
 * Content model builder
 *
 * @author valentin-mcs <valentin.claras@myc-sense.fr>
 */
abstract class ModelBuilder
{
    /**
     * @var ForEachExecutor
     */
    protected $forEachExecutor;

    /**
     * @var TwigExecutor
     */
    protected $twigExecutor;

    public function __construct()
    {
        $this->forEachExecutor = new ForEachExecutor();
    }

    /**
     * @param array    $yamlForeach
     * @param Scope    $scope
     * @param callable $callback
     * @param array    $parameters
     * @return int Number of iteration (where $callback is called) made by the loop for the given scope.
     */
    protected function parseForeach($yamlForeach, Scope $scope, callable $callback, array $parameters)
    {
        // Parse the foreach expression.
        $subScopes = $this->forEachExecutor->execute($yamlForeach['foreach'], $scope);

        return $this->doLoop($yamlForeach, $subScopes, $callback, $parameters);
    }

    /**
     * @param array $yamlLoop
     * @param Scope[] $scopes
     * @param callable $callback
     * @param array $parameters
     * @return int Number of iteration (where $callback is called) made by the loop for the given scope.
     */
    protected function doLoop($yamlLoop, array $scopes, callable $callback, array $parameters)
    {
        if (!array_key_exists('do', $yamlLoop)) {
            return 0;
        }

        $iterationCallback = 0;

        foreach ($scopes as $scope) {
            // Traverse all sub-elements.
            foreach ($yamlLoop['do'] as $yamlElement) {
                call_user_func_array($callback, array_merge($parameters, [$yamlElement, $scope, $iterationCallback]));
                $iterationCallback ++;
            }
        }

        return $iterationCallback;
    }
}
