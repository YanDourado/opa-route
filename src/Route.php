<?php

declare (strict_types = 1);

namespace OpaRoute;

use OpaRoute\Route\Callback;

class Route
{
    /**
     * Route URI
     */
    private string $uri;

    /**
     * Route HTTP methods
     */
    private array $methods;

    /**
     * Route name
     */
    private string $name;

    /**
     * Route callback
     *
     * @var mixed
     */
    private $callback;

    public function __construct(array $methods, string $uri, $callback)
    {
        $this->uri      = $uri;
        $this->methods  = $methods;
        $this->callback = new Callback($callback);
        $this->name     = '';
    }

    /**
     * Define route name
     *
     * @param string $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return route callback
     *
     * @return Callback
     */
    public function getCallback(): Callback
    {
        return $this->callback;
    }

    /**
     * Return route URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Return route HTTP methods
     *
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Return route name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
