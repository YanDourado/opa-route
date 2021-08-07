<?php

declare (strict_types = 1);

namespace OpaRoute;

use OpaRoute\Collections\RouteCollection;
use OpaRoute\Route;
use Symfony\Component\HttpFoundation\Request;

class Router
{

    /**
     * Array with all routes
     */
    public RouteCollection $routes;

    /**
     *  HTTP Request
     */
    private Request $request;

    /**
     * Route prefix for groups
     */
    private string $prefix;

    /**
     * Route namespace for groups
     */
    private string $namespace;

    public function __construct()
    {
        $this->routes  = new RouteCollection();
        $this->request = self::createRequest();
    }

    /**
     * Add GET route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return Route
     */
    public function get(string $uri, $callback, ?string $name = null): Route
    {
        return $this->addRoute(['GET'], $uri, $callback, $name);
    }

    /**
     * Add POST route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return Route
     */
    public function post(string $uri, $callback, ?string $name = null): Route
    {
        return $this->addRoute(['POST'], $uri, $callback, $name);
    }

    /**
     * Add PUT route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return Route
     */
    public function put(string $uri, $callback, ?string $name = null): Route
    {
        return $this->addRoute(['PUT'], $uri, $callback, $name);
    }

    /**
     * Add DELETE route to routes array
     *
     * @param string $uri
     * @param mixed $callback
     * @return Route
     */
    public function delete(string $uri, $callback, ?string $name = null): Route
    {
        return $this->addRoute(['DELETE'], $uri, $callback, $name);
    }

    /**
     * Set group parameters
     *
     * @param array $paramteres
     * @return void
     */
    private function beforeGroup(array $paramteres): void
    {
        $this->prefix    = ($this->prefix ?? '') . ($paramteres['prefix'] ?? '');
        $this->namespace = ($this->namespace ?? '') . ($paramteres['namespace'] ?? '');
    }

    /**
     * Reset group parameters
     *
     * @param array $paramteres
     * @return void
     */
    private function afterGroup(array $paramteres): void
    {
        if ($this->prefix && isset($paramteres['prefix'])) {
            $this->prefix = str_replace($paramteres['prefix'], '', $this->prefix);
        }

        if ($this->namespace && isset($paramteres['namespace'])) {
            $this->namespace = str_replace($paramteres['namespace'], '', $this->namespace);
        }
    }

    /**
     * Create a group of routes
     *
     * @param array $paramters
     * @param callable $routes
     * @return void
     */
    public function group(array $paramteres, callable $callback): void
    {
        $this->beforeGroup($paramteres);
        $callback($this);
        $this->afterGroup($paramteres);
    }

    /**
     * Create a route group with URI prefix
     *
     * @param string $prefix
     * @param callable $callback
     * @return void
     */
    public function prefix(string $prefix, callable $callback): void
    {
        $this->group([
            'prefix' => $prefix
        ], $callback);
    }

    /**
     * Return all routes;
     *
     * @return array
     */
    public function routes(): array
    {
        return $this->routes->allRoutes();
    }

    /**
     * Get all routes created by HTTP method
     *
     * @param string $method
     * @return array|null
     */
    public function getRoutesByMethod(string $method): ?array
    {
        return $this->routes->getRoutesByMethod($method);
    }

    /**
     * Return all route except routes with HTTP method
     *
     * @param string $method
     * @return array|null
     */
    public function getRoutesWithoutMethod(string $method): ?array
    {
        return $this->routes->getRoutesWithoutMethod($method);
    }

    /**
     * Return URI and METHOD from HTTP request
     *
     * @return Request
     */
    public function request(): Request
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
        $dispatcher->dispatch($this->request)
            ->prepare($this->request())
            ->send();
    }

    /**
     * Add Route to routes array
     *
     * @param string $method
     * @param string $uri
     * @param mixed $callback
     * @return Route
     */
    private function addRoute(array $methods, string $uri, $callback, ?string $name = null): Route
    {
        if (isset($this->prefix)) {
            $uri = $this->prefix . $uri;
        }

        if (isset($this->namespace) && true === is_string($callback)) {
            $callback = $this->namespace . $callback;
        }

        $route = new Route($methods, $uri, $callback);
        $this->routes->add($route);
        return $route;
    }

    /**
     * Create Request (URI and METHOD) from Globals
     *
     * @return Request
     */
    private static function createRequest(): Request
    {
        return Request::createFromGlobals();
    }
}
