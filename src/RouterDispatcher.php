<?php

namespace OpaRoute;

class RouterDispatcher
{
    /**
     * Array with all routes
     */
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
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
        $methodRoutes = $this->getRoutesByMethod($method);

        if (null === $methodRoutes
            && $this->routeExistsWithAnotherMethod($method, $uri)) {
            throw new \Exception('Method not allowed');
        }

        $route = $this->findRoute($methodRoutes, $uri);

        if (null === $route) {
            throw new \Exception('Route not found');
        }

        return $this->handle($route);
    }

    /**
     * Get all routes created by HTTP method
     *
     * @param string $method
     * @return array|null
     */
    private function getRoutesByMethod(string $method): ?array
    {
        return $this->routes[$method] ?? null;
    }

    /**
     * Find a route by URI Request
     *
     * @param array $routes
     * @param string $uri
     * @return array|null
     */
    private function findRoute(array $routes, string $uri): ?array
    {
        $key = array_search($uri, array_column($routes, 'uri'));
        return (false !== $key) ? $routes[$key] : null;
    }

    /**
     * Handle route callback
     *
     * @param array $route
     * @return mixed
     */
    private function handle(array $route)
    {
        $response = $route['callback']();

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
        $routesLessMethod = $this->routes;
        unset($routesLessMethod[$method]);
        array_values($routesLessMethod);
        return (bool) null !== $this->findRoute($routesLessMethod, $uri);
    }
}
