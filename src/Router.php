<?php

declare (strict_types = 1);

namespace OpaRoute;

class Router
{

    /**
     * Array with all routes
     */
    private array $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * Add GET route to routes array
     *
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    public function get(string $uri, callable $callback, ?string $name = null): void
    {
        $this->addRoute('GET', $uri, $callback, $name);
    }

    /**
     * Add POST route to routes array
     *
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    public function post(string $uri, callable $callback, ?string $name = null): void
    {
        $this->addRoute('POST', $uri, $callback, $name);
    }

    /**
     * Add PUT route to routes array
     *
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    public function put(string $uri, callable $callback, ?string $name = null): void
    {
        $this->addRoute('PUT', $uri, $callback, $name);
    }

    /**
     * Add DELETE route to routes array
     *
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    public function delete(string $uri, callable $callback, ?string $name = null): void
    {
        $this->addRoute('DELETE', $uri, $callback, $name);
    }

    /**
     * Return all routes;
     *
     * @return array
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Add Route to routes array
     *
     * @param string $method
     * @param string $uri
     * @param callable $callback
     * @return void
     */
    private function addRoute(string $method, string $uri, callable $callback, ?string $name = null): void
    {
        $route = [
            'uri'      => $uri,
            'callback' => $callback
        ];

        if (null !== $name) {
            $route['name'] = $name;
        }

        $this->routes[$method][] = $route;
    }
}
