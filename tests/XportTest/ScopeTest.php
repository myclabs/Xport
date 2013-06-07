<?php

namespace XportTest;

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
}
