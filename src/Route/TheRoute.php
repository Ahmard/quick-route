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
    private string $method;
    private $controller;

    public function __construct(array $withData = [])
    {
        if ($withData) {
            $this->isWithUsed = true;
            $this->namespace = $withData['namespace'] ?? '';
            $this->name = $withData['name'] ?? '';
            $this->prefix = $withData['prefix'] ?? '';
            $this->controller = $withData['controller'] ?? '';
            $this->middleware = $withData['middleware'] ?? '';
            $this->method = $withData['method'] ?? '';
            $this->prepend = $withData['prepend'] ?? '';
            $this->append = $withData['append'] ?? '';
        }
    }

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
        ];
    }

    /**
     * @param string $prefix
     * @return TheRoute $this
     */
    public function prefix(string $prefix): self
    {
        if ($this->prefix && !$this->isWithUsed) {
            $newRouter = new self([
                'namespace' => $this->namespace,
                'prefix' => $this->buildPrefix($this->prefix, $prefix),
                'name' => $this->name,
                'middleware' => $this->middleware,
                'prepend' => $this->prepend,
                'append' => $this->append,
            ]);

            Route::addRoute($newRouter);

            return $newRouter;
        }

        $this->prefix = $this->buildPrefix($this->prefix, $prefix);

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
     * Group controllers
     * @param callable $closure
     * @return TheRoute $this
     */
    public function group(callable $closure): self
    {
        $closure($this);
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
        $this->namespace .= $namespace;
        return $this;
    }

    /**
     * Add name to listener groups
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name .= $name;
        return $this;
    }

    /**
     * Set middleware to the route
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware): self
    {
        $this->middleware .= $middleware;
        return $this;
    }

    /**
     * Prepend string to nn url
     * @param string $prefixToPrepend
     * @return $this
     */
    public function prepend(string $prefixToPrepend): self
    {
        $this->prepend .= $prefixToPrepend;
        return $this;
    }

    /**
     * Append string to url
     * @param string $prefixToAppend
     * @return $this
     */
    public function append(string $prefixToAppend): self
    {
        $this->append .= $prefixToAppend;
        return $this;
    }

    /**
     * Register route in this class
     * @return $this
     */
    public function onRegister(): RouteInterface
    {
        $this->prefix = $this->buildPrefix($this->prepend, $this->prefix);
        $this->prefix = $this->buildPrefix($this->prefix, $this->append);

        if ($this->prefix[0] != '/') {
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
        $newRouter = new self([
            'namespace' => $this->namespace,
            'prefix' => $this->buildPrefix($this->prefix, $route),
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'method' => $method,
            'controller' => $controllerClass,
            'middleware' => $this->middleware,
        ]);

        Route::addRoute($newRouter);

        return $newRouter;
    }

}
