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
     * Add GET to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function get(string $route, $handler): RouteInterface;

    /**
     * Add POST to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function post(string $route, $handler): RouteInterface;

    /**
     * Add PUT to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function put(string $route, $handler): RouteInterface;

    /**
     * Add PATCH to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function patch(string $route, $handler): RouteInterface;

    /**
     * Add DELETE to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function delete(string $route, $handler): RouteInterface;

    /**
     * Add HEAD to route collection
     * @param string $route
     * @param mixed $handler
     * @return $this
     */
    public function head(string $route, $handler): RouteInterface;

    /**
     * Retrieve registered routes
     * @return string[]
     */
    public function getRouteData();

    /**
     * Register route to nikita popov's router
     * @return $this
     */
    public function onRegister(): RouteInterface;
}