<?php

namespace Xport\Parser;

use Twig_Environment;
use Twig_Loader_String;
use Twig_SimpleFunction;

/**
 * Twig parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TwigExecutor
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param callable[] $functions
     */
    public function __construct($functions = [])
    {
        $loader = new Twig_Loader_String();
        $this->twig = new Twig_Environment($loader);

        foreach ($functions as $name => $function) {
            $this->twig->addFunction(new Twig_SimpleFunction($name, $function));
        }
    }

    /**
     * Parse a Twig expression.
     *
     * @param string $expression Twig expression
     * @param Scope  $scope
     *
     * @return string
     */
    public function parse($expression, Scope $scope)
    {
        return $this->twig->render($expression, $scope->toArray());
    }
}
