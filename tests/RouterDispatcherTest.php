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

}
