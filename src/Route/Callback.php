<?php

declare (strict_types = 1);

namespace OpaRoute\Route;

class Callback
{

    /**
     * Types of routes callback
     */
    private const TYPE_CALLABLE = 1, TYPE_STRING = 2, TYPE_ARRAY = 3;

    /**
     * Route callback
     *
     * @var mixed
     */
    private $callback;

    /**
     * Callback controller
     */
    private string $controller = '';

    /**
     * Calback method
     */
    private string $method = '';

    private int $typeCallback;

    public function __construct($callback)
    {
        $this->callback = $callback;
        $this->checkTypeCallback();
    }

    /**
     * Verify what type route callback is
     *
     * @return void
     */
    private function checkTypeCallback(): void
    {
        if (true === is_string($this->callback)) {
            $this->typeCallback = Callback::TYPE_STRING;
            return;
        }

        if (true === is_array($this->callback)) {
            $this->typeCallback = Callback::TYPE_ARRAY;
            return;
        }

        if (true === is_callable($this->callback)) {
            $this->typeCallback = Callback::TYPE_CALLABLE;
            return;
        }

        throw new \InvalidArgumentException('Invalid route callback');
    }

    /**
     * Handle route callback
     *
     * @return mixed
     */
    public function handle(array $parameters = [])
    {
        if (Callback::TYPE_STRING === $this->typeCallback || Callback::TYPE_ARRAY === $this->typeCallback) {
            $this->prepareController()
                ->checkController();
        }

        $reflection = $this->getReflection();
        $args       = $this->getArguments($reflection, $parameters);

        if (true === is_callable($this->callback)) {
            $response = $this->handleFunction($args);
        }

        if (true === is_string($this->callback)) {
            $response = $this->handleController($this->getInstace($this->controller), $args);
        }

        return $response;
    }

    /**
     * Return reflection of callback
     *
     * @return \ReflectionFunctionAbstract
     */
    private function getReflection(): \ReflectionFunctionAbstract
    {
        if (true === is_string($this->callback)) {
            return new \ReflectionMethod($this->controller, $this->method);
        }

        if (true === is_array($this->callback)) {
            return new \ReflectionMethod($this->controller, $this->method);
        }

        if (true === is_callable($this->callback)) {
            return new \ReflectionFunction($this->callback);
        }
    }

    /**
     * Prepare controller to execute route callback
     *
     * @return Callback
     */
    private function prepareController(): self
    {
        if (true === is_string($this->callback)) {
            list($this->controller, $this->method) = explode('@', $this->callback);
        }

        if (true === is_array($this->callback)) {
            list($this->controller, $this->method) = $this->callback;
        }

        return $this;
    }

    /**
     * Validate if Controller class and method exist
     *
     * @return void
     */
    private function checkController(): void
    {
        if (false === class_exists($this->controller)) {
            throw new \Exception("Class {$this->controller} don't exist.");
        }

        if (false === method_exists($this->controller, $this->method)) {
            throw new \Exception("Method {$this->method} don't exist in {$this->controller} class.");
        }
    }

    /**
     * Create a new instance of route callback
     *
     * @param string $namespace
     * @return void
     */
    private function getInstace(string $namespace)
    {
        return new $namespace();
    }

    /**
     * Return callback arguments
     *
     * @param \ReflectionFunctionAbstract $reflection
     * @param array $parameters
     * @return array
     */
    private function getArguments(\ReflectionFunctionAbstract $reflection, array $parameters = []): array
    {
        $args             = [];
        $parametersValues = array_values($parameters);
        foreach ($reflection->getParameters() as $key => $parameter) {
            if (null !== $type = $parameter->getType()) {
                settype($parametersValues[$key], (string) $type);
            }

            $args[] = $parametersValues[$key];
        }

        return $args;
    }

    /**
     * Handle function route
     *
     * @return mixed
     */
    private function handleFunction(array $args = [])
    {
        return call_user_func($this->callback, ...$args);
    }

    /**
     * Handle function in a Controller
     *
     * @param mixed $instance
     * @param string $callback
     * @return mixed
     */
    private function handleController($instance, array $args)
    {
        return $instance->{$this->method}(...$args);
    }

}
