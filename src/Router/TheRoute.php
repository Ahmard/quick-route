<?php

namespace QuickRoute\Router;

use QuickRoute\RouteInterface;

class TheRoute implements RouteInterface
{
    /**
     * Register all request methods
     */
    use RequestMethods;


    private bool $isWithUsed = false;
    private string $prefix = '';
    private string $namespace = '';
    private string $middleware = '';
    private string $name = '';
    private string $append = '';
    private string $prepend = '';
    private string $method = '';
    private array $fields = [];

    /**
     * @var mixed Route handler/handler
     */
    private $handler;

    /**
     * @var callable Route group
     */
    private $group;

    /**
     * @inheritDoc
     */
    public function getRouteData(): array
    {
        $this->onRegister();

        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'handler' => $this->handler,
            'middleware' => $this->middleware,
            'method' => $this->method,
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'group' => $this->group,
            'fields' => $this->fields,
        ];
    }

    /**
     * @inheritDoc
     */
    public function onRegister(): RouteInterface
    {
        if (substr($this->prefix, 0, 1) != Getter::getDelimiter()) {
            $this->prefix = Getter::getDelimiter() . $this->prefix;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prefix(string $prefix): RouteInterface
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function append(string $prefix): RouteInterface
    {
        $this->append = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend(string $prefix): RouteInterface
    {
        $this->prepend = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function group(callable $closure): RouteInterface
    {
        $this->group = $closure;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function namespace(string $namespace): RouteInterface
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(string $name): RouteInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function middleware(string $middleware): RouteInterface
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Listen to route
     * @param string $method
     * @param string $route
     * @param mixed $handlerClass
     * @return TheRoute $this
     */
    private function addRoute(string $method, string $route, $handlerClass): RouteInterface
    {
        $this->method = $method;
        $this->prefix = $route;
        $this->handler = $handlerClass;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(string $name, $value): RouteInterface
    {
        $this->fields[$name] = $value;
        return $this;
    }
}
