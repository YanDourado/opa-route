<?php

declare (strict_types = 1);

namespace OpaRoute;

class Router
{

    /**
     * Array with all routes
     */
    private array $routes;

    /**
     *  HTTP Request
     */
    private array $request;

    public function __construct()
    {
        $this->routes  = [];
        $this->request = self::createRequest();
    }

    /**
     * Add GET route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return void
     */
    public function get(string $uri, $callback, ?string $name = null): void
    {
        $this->addRoute('GET', $uri, $callback, $name);
    }

    /**
     * Add POST route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return void
     */
    public function post(string $uri, $callback, ?string $name = null): void
    {
        $this->addRoute('POST', $uri, $callback, $name);
    }

    /**
     * Add PUT route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return void
     */
    public function put(string $uri, $callback, ?string $name = null): void
    {
        $this->addRoute('PUT', $uri, $callback, $name);
    }

    /**
     * Add DELETE route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return void
     */
    public function delete(string $uri, $callback, ?string $name = null): void
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
     * Get all routes created by HTTP method
     *
     * @param string $method
     * @return array|null
     */
    public function getRoutesByMethod(string $method): ?array
    {
        return $this->routes[$method] ?? null;
    }

    /**
     * Get all route except routes with HTTP method
     */
    public function getRoutesWithoutMethod(string $method): ?array
    {
        $routes = $this->routes();
        unset($routes[$method]);
        $routes = array_values($routes);
        $routes = array_merge(...$routes);
        return $routes;
    }

    /**
     * Return URI and METHOD from HTTP request
     *
     * @return array
     */
    public function request(): array
    {
        return $this->request;
    }

    /**
     * Find and execute function route
     *
     * @return mixed
     */
    public function execute()
    {
        $dispatcher = new RouterDispatcher($this);
        echo $dispatcher->dispatch(
            $this->request['uri'],
            $this->request['method']
        );
    }

    /**
     * Add Route to routes array
     *
     * @param string $method
     * @param string $uri
     * @param mixed $callback
     * @return void
     */
    private function addRoute(string $method, string $uri, $callback, ?string $name = null): void
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

    /**
     * Create Request (URI and METHOD) from Globals
     *
     * @return array
     */
    private static function createRequest(): array
    {
        return [
            'uri'    => isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : null,
            'method' => isset($_SERVER['REQUEST_METHOD']) ? parse_url($_SERVER['REQUEST_METHOD'], PHP_URL_PATH) : null
        ];
    }
}
