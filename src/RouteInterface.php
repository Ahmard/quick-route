<?php


namespace QuickRoute;


interface RouteInterface
{
    /**
     * HttpServer request prefix
     * @param string $prefix
     * @return $this
     */
    public function prefix(string $prefix): RouteInterface;

    /**
     * Group http requests together
     * @param callable $closure
     * @return $this
     */
    public function group(callable $closure): RouteInterface;

    /** Set namespace to group of routes
     * @param string $namespace
     * @return $this
     */
    public function namespace(string $namespace): RouteInterface;

    /** Set namespace to group of routes
     * @param string $middleware
     * @return $this
     */
    public function middleware(string $middleware): RouteInterface;

    /** Set name to group of routes
     * @param string $name
     * @return $this
     */
    public function name(string $name): RouteInterface;

    /**
     * Prepend string to nn url
     * @param string $prefixToPrepend
     * @return $this
     */
    public function prepend(string $prefixToPrepend): RouteInterface;

    /**
     * Append string to url
     * @param string $prefixToAppend
     * @return $this
     */
    public function append(string $prefixToAppend): RouteInterface;

    /**
     *
     * @param string $route
     * @param mixed $controller
     * @return $this
     */
    public function get(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param mixed $controller
     * @return $this
     */
    public function post(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param mixed $controller
     * @return $this
     */
    public function put(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param mixed $controller
     * @return $this
     */
    public function patch(string $route, $controller): RouteInterface;

    /**
     *
     * @param string $route
     * @param mixed $controller
     * @return $this
     */
    public function delete(string $route, $controller): RouteInterface;

    /**
     * Retrieve registered routes
     * @return array
     */
    public function getRouteData(): array;

    /**
     * Register route to nikita popov's router
     * @return $this
     */
    public function onRegister(): RouteInterface;
}