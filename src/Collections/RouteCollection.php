<?php

declare (strict_types = 1);

namespace OpaRoute\Collections;

use OpaRoute\Route;

class RouteCollection
{

    /**
     * All routes
     */
    private array $allRoutes = [];

    /**
     * Routes grouped by HTTP method
     */
    private array $routes = [];

    /**
     * Add route to array
     *
     * @param Route $route
     * @return void
     */
    public function add(Route $route): void
    {
        $this->allRoutes[] = $route;

        foreach ($route->getMethods() as $method) {
            $this->routes[$method][] = $route;
        }
    }

    /**
     * Return all routes
     *
     * @return array
     */
    public function allRoutes(): array
    {
        return $this->allRoutes;
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
     * Return all route except routes with HTTP method
     *
     * @param string $method
     * @return array|null
     */
    public function getRoutesWithoutMethod(string $method): ?array
    {
        $routes = $this->routes;
        unset($routes[$method]);
        $routes = array_values($routes);
        $routes = array_merge(...$routes);
        return $routes;
    }
}
