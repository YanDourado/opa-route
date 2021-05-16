<?php

namespace OpaRoute\Test;

use OpaRoute\Matcher;
use OpaRoute\Router;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{

    public function testCheckIfNotDynamicRouteExist()
    {
        $router = new Router();
        $router->get('/', function () {});
        $router->get('/foo', function () {});
        $router->get('/bar', function () {});

        $matcher    = new Matcher();
        $routeFound = $matcher->findRoute($router->getRoutesByMethod('GET'), '/foo');

        $this->assertSame('/foo', $routeFound->getUri());
    }

    public function testCheckIfDynamicRouteExist()
    {
        $router = new Router();
        $router->get('/', function () {});
        $router->get('/foo', function () {});
        $router->get('/bar', function () {});
        $router->get('/users/{id}', function () {});

        $matcher    = new Matcher();
        $routeFound = $matcher->findRoute($router->getRoutesByMethod('GET'), '/users/1');

        $this->assertSame('/users/{id}', $routeFound->getUri());
    }

    public function testCheckDynamicRouteParameters()
    {
        $router = new Router();
        $router->get('/foo/{foo}/bar/{bar}', function () {});

        $matcher = new Matcher();
        $matcher->findRoute($router->getRoutesByMethod('GET'), '/foo/hello/bar/world');

        $this->assertSame(['foo' => 'hello', 'bar' => 'world'], $matcher->parameters());
    }
}
