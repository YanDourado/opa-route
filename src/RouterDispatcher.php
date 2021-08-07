<?php

namespace OpaRoute;

use OpaRoute\Matcher;
use OpaRoute\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RouterDispatcher
{
    /**
     * Array with all routes
     */
    private Router $routes;

    private Matcher $matcher;

    private Response $response;

    public function __construct(Router $router)
    {
        $this->router   = $router;
        $this->matcher  = new Matcher();
        $this->response = new Response();
    }

    /**
     * Dispatch a route with base on Request URI and Method
     *
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri    = $request->getPathInfo();

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

        return $this->handle($route, $request);
    }

    /**
     * Handle route callback
     *
     * @param Route $route
     * @param Request $request
     * @return Response
     */
    private function handle(Route $route, Request $request): Response
    {
        $callback = $route->getCallback();

        $response = $callback->handle($request, $this->matcher->parameters());

        if ($response instanceof Response) {
            $this->response = $response;
        } else if (true === is_array($response) || true === is_object($response)) {
            $this->response = new Response(json_encode($response));
        } else if (is_string($response)) {
            $this->response = new Response($response);
        }

        return $this->response;
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
