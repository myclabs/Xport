<?php

namespace XportTest\Parser;

use Xport\Parser\ForEachExecutor;
use Xport\Parser\Scope;

class ForEachExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $scope = new Scope();
        $scope->bind('foo', ['barValue0', 'barValue1']);

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo as bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithKey1()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo as bim => bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithKey2()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo as bim=>bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
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

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('  foo.blah[bleh]  as      bim  =>    bar ', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithFunction1()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);
        $scope->bindFunction('foo', function ($parameter) { return ucfirst($parameter); });

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo as bim => foo(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('BarValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithFunction2()
    {
        $scope = new Scope();
        $scope->bind('foo', ['bimIndex0' => 'barValue0', 'bimIndex1' => 'barValue1']);
        $scope->bindFunction('foo', function ($parameter) { return ucfirst($parameter); });

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo as foo(bim) => foo(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
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

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('blah(foo) as bleh(bim) => bleh(bar)', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('BimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('BarValue'.$i, $subScope->get('bar'));
        }
    }
}
