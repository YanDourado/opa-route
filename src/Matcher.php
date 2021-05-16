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
        return $this->parameters;
    }

    /**
     * Set URL paramenters values
     *
     * @param array $parametersValue
     * @return void
     */
    private function setParametersValues(array $parametersValue): void
    {
        $parameters = [];
        foreach ($this->parameters as $key => $value) {
            $parameters[$value] = $parametersValue[$key];
        }
        $this->parameters = $parameters;
    }

    /**
     * Reset URL paramenters values
     *
     * @return void
     */
    private function resetParameters(): void
    {
        $this->parameters = [];
    }

    /**
     * Find a route by URI Request
     *
     * @param array $routes
     * @param string $requestUri
     * @return Route|null
     */
    public function findRoute(array $routes, string $requestUri): ?Route
    {
        foreach ($routes as $route) {
            if (true === $this->isEquals($route->getUri(), $requestUri)) {
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
        $this->resetParameters();
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
        return preg_replace_callback('/\{(.*?)\}/', function ($matches) {
            $this->parameters[] = $matches[1];
            return '([a-zA-Z0-9\-\_]+)';
        }, $uri);
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
        if ($match = (bool) preg_match($pattern, $requestUri, $parameters)) {
            $this->setParametersValues(array_slice($parameters, 1));
        }
        return $match;
    }

}
