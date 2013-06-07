<?php

namespace Xport\SpreadsheetModel\Parser;

use Twig_Environment;
use Twig_Loader_String;

/**
 * Twig parser
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class TwigParser
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $loader = new Twig_Loader_String();
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Parse a Twig expression.
     *
     * @param string $str Twig expression
     * @param Scope  $scope
     *
     * @return string
     */
    public function parse($str, Scope $scope)
    {
        return $this->twig->render($str, $scope->toArray());
    }
}
