<?php

namespace OpaRoute;

use OpaRoute\Matcher;
use OpaRoute\Router;

class RouterDispatcher
{
    /**
     * Array with all routes
     */
    private Router $routes;

    private Matcher $matcher;

    public function __construct(Router $router)
    {
        $this->router  = $router;
        $this->matcher = new Matcher();
    }

    /**
     * Dispatch a route with base on Request URI and Method
     *
     * @param string $uri
     * @param string $method
     * @return mixed
     */
    public function dispatch(string $uri, string $method)
    {
        $methodRoutes = $this->router->getRoutesByMethod($method);

        if (null === $methodRoutes
            && true === $this->routeExistsWithAnotherMethod($method, $uri)) {
            throw new \Exception('Method not allowed');
        }

        $route = $this->matcher->findRoute($methodRoutes, $uri);

        if (null === $route
            && true === $this->routeExistsWithAnotherMethod($method, $uri)) {
            throw new \Exception('Method not allowed');
        }

        if (null === $route) {
            throw new \Exception('Route not found');
        }

        return $this->handle($route);
    }

    /**
     * Handle route callback
     *
     * @param Route $route
     * @return mixed
     */
    private function handle(Route $route)
    {
        $routeCallback = $route->getCallback();

        if (true === is_callable($routeCallback)) {
            $response = $this->handleFunction($routeCallback);
        }

        if (true === is_string($routeCallback)) {
            $response = $this->handleController($routeCallback);
        }

        if (true === is_array($response)) {
            $response = json_encode($response);
        }

        return $response;
    }

    /**
     * Handle function route
     *
     * @param callable $callback
     * @return mixed
     */
    private function handleFunction(callable $callback)
    {
        return $callback(...$this->matcher->parameters());
    }

    /**
     * Handle function in a Controller
     *
     * @param string $callback
     * @return mixed
     */
    private function handleController(string $callback)
    {
        list($controller, $method) = explode('@', $callback);

        if (false === class_exists($controller)) {
            throw new \Exception("Class $controller don't exist.");
        }

        if (false === method_exists($controller, $method)) {
            throw new \Exception("Method $method don't exist in $controller class.");
        }

        $instance = new $controller();
        return $instance->$method(...$this->matcher->parameters());
    }

    /**
     * Verify if route exists with another HTTP method different Request method
     *
     * @param string $method
     * @param string $uri
     * @return boolean
     */
    private function routeExistsWithAnotherMethod(string $method, string $uri): bool
    {
        $routesLessMethod = $this->router->getRoutesWithoutMethod($method);
        return null !== $this->matcher->findRoute($routesLessMethod, $uri);
    }
}
