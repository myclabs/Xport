<?php

namespace XportTest\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\ForEachParser;
use Xport\SpreadsheetModel\Parser\Scope;

class ForEachParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $scope = new Scope();
        $scope->bind('foo', ['barValue0', 'barValue1']);

        $parser = new ForEachParser();

        $subScopes = $parser->parse('foo as bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithKey1()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);

        $parser = new ForEachParser();

        $subScopes = $parser->parse('foo as bim => bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithKey2()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);

        $parser = new ForEachParser();

        $subScopes = $parser->parse('foo as bim=>bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithKey3()
    {
        $foo = new \stdClass();
        $foo->blah = ['bleh' => ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']];
        $scope = new Scope();
        $scope->bind('foo', $foo);

        $parser = new ForEachParser();

        $subScopes = $parser->parse('  foo.blah[bleh]  as      bim  =>    bar ', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithFunction1()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);
        $scope->bindFunction('foo', function ($parameter) { return ucfirst($parameter); });

        $parser = new ForEachParser();

        $subScopes = $parser->parse('foo as bim => foo(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('BarValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithFunction2()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);
        $scope->bindFunction('foo', function ($parameter) { return ucfirst($parameter); });

        $parser = new ForEachParser();

        $subScopes = $parser->parse('foo as foo(bim) => foo(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('BimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('BarValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithFunction3()
    {
        $scope = new Scope();
        $scope->bind('foo', ['0', '1']);
        $scope->bindFunction('blah', function ($parameters) { $a=[]; foreach($parameters as $i) {$a['bimIndex'.$i] = 'barValue'.$i;} return $a; });
        $scope->bindFunction('bleh', function ($parameter) { return ucfirst($parameter); });

        $parser = new ForEachParser();

        $subScopes = $parser->parse('blah(foo) as bleh(bim) => bleh(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\SpreadsheetModel\Parser\Scope', $subScope);
            $this->assertEquals('BimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('BarValue'.$i, $subScope->get('bar'));
        }
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString1()
    {
        $parser = new ForEachParser();
        $parser->parse('', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString2()
    {
        $parser = new ForEachParser();
        $parser->parse('foobar', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString3()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar as test', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString4()
    {
        $parser = new ForEachParser();
        $parser->parse('foo => bar as test', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString5()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString6()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar =>', new Scope());
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString7()
    {
        $parser = new ForEachParser();
        $parser->parse('foo bar', new Scope());
    }
}
