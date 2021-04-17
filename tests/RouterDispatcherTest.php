<?php

namespace OpaRoute\Test;

use OpaRoute\Router;
use OpaRoute\RouterDispatcher;
use PHPUnit\Framework\TestCase;

class RouterDispatcherTest extends TestCase
{

    public function testIfRouteExistItHasToCall()
    {
        $router = new Router();

        $router->get('/', function () {
            return 'Hello World!';
        });

        $router->post('/', function () {
            return ['name' => 'Yan Dourado'];
        });

        $dispatcher = new RouterDispatcher($router);
        $dispatcher = new RouterDispatcher($router);

        $this->assertEquals('Hello World!', $dispatcher->dispatch('/', 'GET'));
        $this->assertEquals(json_encode(['name' => 'Yan Dourado']), $dispatcher->dispatch('/', 'POST'));
    }

    public function testDifferentsFunctionHandleMustBeExecute()
    {
        $router = new Router();

        $router->get('/', function () {
            return 'Hello World! GET';
        });
        $router->post('/', '\OpaRoute\Test\TestController@post');
        $router->put('/', [\OpaRoute\Test\TestController::class, 'put']);

        $dispatcher = new RouterDispatcher($router);

        $this->assertEquals('Hello World! GET', $dispatcher->dispatch('/', 'GET'));
        $this->assertEquals('Hello World! POST', $dispatcher->dispatch('/', 'POST'));
        $this->assertEquals('Hello World! PUT', $dispatcher->dispatch('/', 'PUT'));
    }

    public function testErroRouteNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Route not found');

        $router = new Router();

        $router->get('/', function () {
            return 'Hello World!';
        });

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch('/foo', 'GET');
    }

    public function testErroMethodNotAllowed()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Method not allowed');

        $router = new Router();

        $router->get('/foo', function () {
            return 'Hello World!';
        });

        $router->post('/', function () {
            return 'Hello World!';
        });

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch('/', 'GET');
    }

    public function testErrorClassNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage("Class \OpaRoute\Test\FooController don't exist.");

        $router = new Router();
        $router->post('/', '\OpaRoute\Test\FooController@test');

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch('/', 'POST');
    }

    public function testErrorMethodNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage("Method test don't exist in \OpaRoute\Test\TestController class.");

        $router = new Router();
        $router->post('/', '\OpaRoute\Test\TestController@test');

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch('/', 'POST');
    }
}

class TestController
{
    public function post()
    {
        return 'Hello World! POST';
    }

    public function put()
    {
        return 'Hello World! PUT';
    }
}
