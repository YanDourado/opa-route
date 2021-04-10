<?php

namespace YanDourado\Test;

use OpaRoute\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    public function testAddRoutesInRouter()
    {
        $router = new Router();

        $router->get('/', function () {});
        $router->post('/', function () {});
        $router->put('/', function () {});
        $router->delete('/', function () {});

        $routes = [
            'GET'    => [['uri' => '/', 'callback' => function () {}]],
            'POST'   => [['uri' => '/', 'callback' => function () {}]],
            'PUT'    => [['uri' => '/', 'callback' => function () {}]],
            'DELETE' => [['uri' => '/', 'callback' => function () {}]]
        ];

        $this->assertEquals($routes, $router->routes());
    }

    public function testAddTwoOrMoreRoutesWithSameHttpVerbInRouter()
    {
        $router = new Router();

        $router->get('/foo', function () {});
        $router->get('/bar', function () {});

        $routes = [
            'GET' => [
                ['uri' => '/foo', 'callback' => function () {}],
                ['uri' => '/bar', 'callback' => function () {}]
            ]
        ];

        $this->assertEquals($routes, $router->routes());
    }

    public function testAddRouteWithNameInRouter()
    {
        $router = new Router();

        $router->get('/', function () {}, 'index');

        $routes = [
            'GET' => [['uri' => '/', 'callback' => function () {}, 'name' => 'index']]
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

}
