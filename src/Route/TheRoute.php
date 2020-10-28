<?php

namespace QuickRoute\Route;

use QuickRoute\Route;
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
     * @param string $prefix
     * @return TheRoute $this
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
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
     * Add namespace to listener groups
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
     * Add name to listener groups
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
        if ($this->prefix[0] != '/') {
            $this->prefix = '/' . $this->prefix;
        }

        return $this;
    }

    protected function buildPrefix(string $prefix1, string $prefix2)
    {
        $prefix2 = $this->removeTrailingSlash($prefix2);
        if ($prefix2 && $prefix2 != '/') {
            return $prefix1 . '/' . $prefix2;
        }

        return empty($prefix1) ? '/' : $prefix1;
    }

    protected function removeTrailingSlash(string $prefix)
    {
        $totalStr = strlen($prefix) - 1;
        if ($totalStr > 0) {
            if ($prefix[$totalStr] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 0, $totalStr);
            }

            if ($prefix[0] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 1, $totalStr + 1);
            }
        }

        return $prefix;
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
        $this->prefix = $this->buildPrefix($this->prefix, $route);
        $this->controller = $controllerClass;
        return $this;
    }

}
