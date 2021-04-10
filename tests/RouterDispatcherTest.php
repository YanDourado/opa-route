<?php

namespace OpaRoute\Test;

use OpaRoute\Router;
use PHPUnit\Framework\TestCase;

class RouterDispatcherTest extends TestCase
{

    public function testIfRouteExistItHasToCall()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();

        $router->get('/', function () {
            return 'Hello World!';
        });

        $this->assertEquals('Hello World!', $router->execute());

        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();

        $router->get('/', function () {
            return ['name' => 'Yan Dourado'];
        });

        $this->assertEquals(json_encode(['name' => 'Yan Dourado']), $router->execute());
    }

    public function testErroRouteNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Route not found');

        $_SERVER['REQUEST_URI']    = '/foo';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $router = new Router();
        $router->get('/', function () {});
        $router->execute();
    }

    public function testErroMethodNotAllowed()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Method not allowed');

        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new Router();
        $router->get('/', function () {});
        $router->execute();
    }

}
