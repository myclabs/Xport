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

    public function testWithFunction()
    {
        $scope = new Scope();
        $scope->bind('foo', ['0', '1']);
        $scope->bindFunction('blah', function ($parameters) { $a=[]; foreach($parameters as $i) {$a['bimIndex'.$i] = 'barValue'.$i;} return $a; });

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('blah(foo) as bim => bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('bimIndex'.$i, $subScope->get('bim'));
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }

    public function testWithMethodCall()
    {

        $scope = new Scope();
        $scope->bind('foo', new FixtureClass());

        $parser = new ForEachExecutor();

        $subScopes = $parser->execute('foo.getStuff() as bar', $scope);

        $this->assertCount(2, $subScopes);

        foreach ($subScopes as $i => $subScope) {
            $this->assertInstanceOf('Xport\Parser\Scope', $subScope);
            $this->assertEquals('barValue'.$i, $subScope->get('bar'));
        }
    }
}

class FixtureClass
{
    public function getStuff()
    {
        return ['barValue0', 'barValue1'];
    }
}
