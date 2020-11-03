<?php

namespace QuickRoute\Route;

use QuickRoute\RouteInterface;

class TheRoute implements RouteInterface
{
    /**
     * Register all request methods
     */
    use RequestMethods;


    public bool $isWithUsed = false;
    private string $prefix = '';
    private string $namespace = '';
    private string $middleware = '';
    private string $name = '';
    private string $append = '';
    private string $prepend = '';
    private string $method = '';
    private $controller = '';
    private $group = null;

    /**
     * Retrieve controllers defined in this object
     * @return array
     */
    public function getRouteData(): array
    {
        $this->onRegister();

        return [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'controller' => $this->controller,
            'middleware' => $this->middleware,
            'method' => $this->method,
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'group' => $this->group,
        ];
    }

    /**
     * Give a group of routes a prefix
     * @param string $prefix
     * @return TheRoute $this
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Append string of prefix to route
     * @param string $prefix
     * @return TheRoute $this
     */
    public function append(string $prefix): self
    {
        $this->append = $prefix;
        return $this;
    }

    /**
     * Prepend string of prefix to route
     * @param string $prefix
     * @return TheRoute $this
     */
    public function prepend(string $prefix): self
    {
        $this->prepend = $prefix;
        return $this;
    }

    /**
     * Group controllers
     * @param callable $closure
     * @return TheRoute $this
     */
    public function group(callable $closure): self
    {
        $this->group = $closure;
        return $this;
    }

    /**
     * Add namespace to group
     * @param string $namespace
     * @return $this
     */
    public function namespace(string $namespace): self
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Add name to route groups
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set middleware to the route
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Register route in this class
     * @return $this
     */
    public function onRegister(): RouteInterface
    {
        if (substr($this->prefix, 0, 1) != '/') {
            $this->prefix = '/' . $this->prefix;
        }

        return $this;
    }

    /**
     * Listen to route
     * @param string $method
     * @param string $route
     * @param callable|string $controllerClass
     * @return TheRoute $this
     */
    public function add(string $method, string $route, $controllerClass): self
    {
        $this->method = $method;
        $this->prefix = $route;
        $this->controller = $controllerClass;
        return $this;
    }

}
