<?php

declare (strict_types = 1);

namespace OpaRoute;

class Matcher
{

    /**
     * Route Dynamic parameters
     */
    private array $parameters = [];

    /**
     * Return parameters found in dynamic route
     *
     * @return array
     */
    public function parameters(): array
    {
        return array_slice($this->parameters, 1);
    }

    /**
     * Find a route by URI Request
     *
     * @param array $routes
     * @param string $requestUri
     * @return array|null
     */
    public function findRoute(array $routes, string $requestUri): ?array
    {
        foreach ($routes as $route) {
            if (true === $this->isEquals($route['uri'], $requestUri)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Verify if route uri is equals request uri
     *
     * @param string $routeUri
     * @param string $requestUri
     * @return boolean
     */
    private function isEquals(string $routeUri, string $requestUri): bool
    {
        $isDynamicRoute = $this->isDynamicRoute($routeUri);

        if (false === $isDynamicRoute) {
            return $routeUri === $requestUri;
        }

        $parsedUri = $this->parseUri($routeUri);
        return true === $this->match($parsedUri, $requestUri);
    }

    /**
     * Check if route is dynamic
     *
     * @param string $routeUri
     * @return boolean
     */
    private function isDynamicRoute(string $routeUri): bool
    {
        return (bool) preg_match_all('/\{(.*?)\}/', $routeUri);
    }

    /**
     * Parse route URI
     *
     * @param string $uri
     * @return string
     */
    private function parseUri(string $uri): string
    {
        preg_match_all('/\{(.*?)\}/', $uri, $matches);

        foreach ($matches[0] as $match) {
            $uri = str_replace($match, '([a-zA-Z0-9\-\_]+)', $uri);
        }
        return $uri;
    }

    /**
     * Check if dynamic uri parsed is equal request uri
     *
     * @param string $parsedUri
     * @param string $requestUri
     * @return boolean
     */
    private function match(string $parsedUri, string $requestUri): bool
    {
        $pattern = "@^$parsedUri$@D";
        return (bool) preg_match($pattern, $requestUri, $this->parameters);
    }

}
