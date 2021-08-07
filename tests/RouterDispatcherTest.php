<?php

namespace OpaRoute\Test;

use OpaRoute\Router;
use OpaRoute\RouterDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $this->assertEquals(new Response('Hello World!'), $dispatcher->dispatch(Request::create('/', 'GET')));
        $this->assertEquals(new Response(json_encode(['name' => 'Yan Dourado'])), $dispatcher->dispatch(Request::create('/', 'POST')));
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

        $this->assertEquals(new Response('Hello World! GET'), $dispatcher->dispatch(Request::create('/', 'GET')));
        $this->assertEquals(new Response('Hello World! POST'), $dispatcher->dispatch(Request::create('/', 'POST')));
        $this->assertEquals(new Response('Hello World! PUT'), $dispatcher->dispatch(Request::create('/', 'PUT')));
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

        $dispatcher->dispatch(Request::create('/foo', 'GET'));
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

        $dispatcher->dispatch(Request::create('/', 'GET'));
    }

    public function testErrorClassNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage("Class \OpaRoute\Test\FooController don't exist.");

        $router = new Router();
        $router->post('/', '\OpaRoute\Test\FooController@test');

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch(Request::create('/', 'POST'));
    }

    public function testErrorMethodNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage("Method test don't exist in \OpaRoute\Test\TestController class.");

        $router = new Router();
        $router->post('/', '\OpaRoute\Test\TestController@test');

        $dispatcher = new RouterDispatcher($router);

        $dispatcher->dispatch(Request::create('/', 'POST'));
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
