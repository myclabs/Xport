<?php

namespace XportTest\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\Scope;
use Xport\SpreadsheetModel\Parser\TwigParser;

class TwigParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $scope = new Scope();
        $scope->bind('foo', 'bar');

        $twigParser = new TwigParser();

        $this->assertEquals('foo', $twigParser->parse('foo', $scope));
        $this->assertEquals('bar', $twigParser->parse('{{ foo }}', $scope));
    }

    public function testWithFunctions()
    {
        $scope = new Scope();
        $scope->bind('foo', 'bar');
        $scope->bindFunction('test', function($str) {
                return strtoupper($str);
            });

        $twigParser = new TwigParser();

        $this->assertEquals('foo', $twigParser->parse('foo', $scope));
        $this->assertEquals('bar', $twigParser->parse('{{ foo }}', $scope));
        $this->assertEquals('BAR', $twigParser->parse('{{ test(foo) }}', $scope));
    }
}
