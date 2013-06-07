<?php

namespace XportTest;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Xport\Scope;

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

    public function testWithPropertyAccess()
    {
        $scope = new Scope();

        $scope->bind('foo', 'bar');

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $value = $propertyAccessor->getValue($scope, 'foo');
        $this->assertEquals('bar', $value);
    }
}
