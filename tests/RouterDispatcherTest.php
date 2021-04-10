<?php

namespace OpaRoute\Test;

use OpaRoute\RouterDispatcher;
use PHPUnit\Framework\TestCase;

class RouterDispatcherTest extends TestCase
{

    public function testIfRouteExistItHasToCall()
    {
        $routes = [
            'GET'  => [
                ['uri' => '/', 'callback' => function () {return 'Hello World!';}]
            ],
            'POST' => [
                ['uri' => '/', 'callback' => function () {return ['name' => 'Yan Dourado'];}]
            ]
        ];

        $dispatcher1 = new RouterDispatcher($routes);
        $dispatcher2 = new RouterDispatcher($routes);

        $this->assertEquals('Hello World!', $dispatcher1->dispatch('/', 'GET'));
        $this->assertEquals(json_encode(['name' => 'Yan Dourado']), $dispatcher2->dispatch('/', 'POST'));
    }

    public function testErroRouteNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Route not found');

        $routes = [
            'GET' => [
                ['uri' => '/', 'callback' => function () {return 'Hello World!';}]
            ]
        ];

        $dispatcher = new RouterDispatcher($routes);

        $dispatcher->dispatch('/foo', 'GET');
    }

    public function testErroMethodNotAllowed()
    {
        $this->expectException(\Exception::class);
        $this->expectErrorMessage('Method not allowed');

        $routes = [
            'POST' => [
                ['uri' => '/', 'callback' => function () {return 'Hello World!';}]
            ]
        ];

        $dispatcher = new RouterDispatcher($routes);

        $dispatcher->dispatch('/', 'GET');
    }

}
