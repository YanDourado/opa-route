<?php

namespace YanDourado\Test;

use OpaRoute\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    public function testAddRoutesInRouter()
    {
        $router = new Router();

        $routerGet = $router->get('/', function () {});
        $routerPost = $router->post('/', function () {});
        $routerPut = $router->put('/', function () {});
        $routerDelete = $router->delete('/', function () {});

        $routes = [
            'GET'    => [$routerGet],
            'POST'   => [$routerPost],
            'PUT'    => [$routerPut],
            'DELETE' => [$routerDelete]
        ];

        $this->assertEquals($routes, $router->routes());
    }

    public function testAddTwoOrMoreRoutesWithSameHttpVerbInRouter()
    {
        $router = new Router();

        $routeFoo = $router->get('/foo', function () {});
        $routeBar = $router->get('/bar', function () {});

        $routes = [
            'GET' => [
                $routeFoo, $routeBar
            ]
        ];

        $this->assertEquals($routes, $router->routes());
    }

    public function testAddRouteWithNameInRouter()
    {
        $router = new Router();

        $route = $router->get('/', function () {}, 'index')->name('opa');

        $routes = [
            'GET' => [$route]
        ];

        $this->assertEquals($routes, $router->routes());
    }

    public function testWhenRouterIsCreatedRequestMustBeToo()
    {
        $_SERVER['REQUEST_URI']    = '/foo';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();

        $this->assertEquals([
            'uri'    => '/foo',
            'method' => 'GET'
        ], $router->request());
    }

    public function testIfRouteExistItMustBeExecute()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();

        $router->get('/', function () {
            return 'Hello World!';
        });

        $router->execute();
        $this->expectOutputString('Hello World!');
    }

    public function testRouteParametersMustBePassedToFunction()
    {
        $_SERVER['REQUEST_URI']    = '/foo/hello/bar/world';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();
        $router->get('/foo/{foo}/bar/{bar}', function ($foo, $bar) {
            return "$foo $bar";
        });

        $router->execute();
        $this->expectOutputString('hello world');
    }

}
