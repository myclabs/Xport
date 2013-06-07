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
        $this->assertEquals('bar', $twigParser->parse('{{foo}}', $scope));
    }
}
