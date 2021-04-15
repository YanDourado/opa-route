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
     * @param array $route
     * @return mixed
     */
    private function handle(array $route)
    {
        $response = $route['callback'](...$this->matcher->parameters());

        if (is_array($response)) {
            $response = json_encode($response);
        }

        return $response;
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
