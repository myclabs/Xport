<?php

namespace XportTest\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\Scope;
use Xport\SpreadsheetModel\Parser\TwigExecutor;

class TwigExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $scope = new Scope();
        $scope->bind('foo', 'bar');

        $twigExecutor = new TwigExecutor($scope->getFunctions());

        $this->assertEquals('foo', $twigExecutor->parse('foo', $scope));
        $this->assertEquals('bar', $twigExecutor->parse('{{ foo }}', $scope));
    }

    public function testWithFunctions()
    {
        $scope = new Scope();
        $scope->bind('foo', 'bar');
        $scope->bindFunction('test', function($str) {
                return strtoupper($str);
            });

        $twigExecutor = new TwigExecutor($scope->getFunctions());

        $this->assertEquals('foo', $twigExecutor->parse('foo', $scope));
        $this->assertEquals('bar', $twigExecutor->parse('{{ foo }}', $scope));
        $this->assertEquals('BAR', $twigExecutor->parse('{{ test(foo) }}', $scope));
        $this->assertEquals('BAR / bar', $twigExecutor->parse('{{ test(foo) }} / {{ foo }}', $scope));
    }

    /**
     * Non-regression test
     */
    public function testSuccessiveParsingWithFunctions()
    {
        $scope = new Scope();
        $scope->bindFunction('test', function($str) {
                return strtoupper($str);
            });

        $twigParser = new TwigExecutor($scope->getFunctions());

        $this->assertEquals('BAR', $twigParser->parse('{{ test("bar") }}', $scope));
        $this->assertEquals('BAR', $twigParser->parse('{{ test("bar") }}', $scope));
    }

}
