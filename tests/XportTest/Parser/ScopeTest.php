<?php

namespace XportTest\Parser;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Xport\Parser\Scope;

class ScopeTest extends \PHPUnit_Framework_TestCase
{
    public function testBindGet()
    {
        $scope = new Scope();

        $scope->bind('foo', 'bar');

        $this->assertEquals('bar', $scope->get('foo'));

        return $scope;
    }

    /**
     * @depends testBindGet
     */
    public function testExtends(Scope $scope)
    {
        $scope = new Scope($scope);

        $this->assertEquals('bar', $scope->get('foo'));
    }

    public function testMagicGet()
    {
        $scope = new Scope();

        $scope->bind('foo', 'bar');

        $this->assertEquals('bar', $scope->foo);
    }

    public function testArrayAccess()
    {
        $scope = new Scope();

        $scope['foo'] = 'bar';

        $this->assertFalse(isset($scope['unknown']));
        $this->assertTrue(isset($scope['foo']));

        $this->assertEquals('bar', $scope['foo']);

        unset($scope['foo']);
        $this->assertFalse(isset($scope['foo']));
    }

    public function testWithPropertyAccess()
    {
        $scope = new Scope();

        $scope->bind('foo', 'bar');

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $value = $propertyAccessor->getValue($scope, 'foo');
        $this->assertEquals('bar', $value);
    }

    public function testToArray()
    {
        $scope = new Scope();
        $scope->bind('foo', 'bar');

        $array = $scope->toArray();

        $this->assertEquals(['foo' => 'bar'], $array);
    }

    public function testBindFunction()
    {
        $scope = new Scope();

        $scope->bindFunction('foo', function() {
                return 'hello';
            });

        $function = $scope->getFunction('foo');
        $this->assertEquals('hello', $function());

        $functions = $scope->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertEquals('hello', $functions['foo']());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown entry for name 'foo'
     */
    public function testGetNotBoundParameter()
    {
        $scope = new Scope();
        $scope->get('foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown function 'foo'
     */
    public function testGetNotBoundFunction()
    {
        $scope = new Scope();
        $scope->getFunction('foo');
    }
}
