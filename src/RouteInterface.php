<?php


namespace QuickRoute;


interface RouteInterface
{
    /**
     * HttpServer request prefix
     * @param string $prefix
     * @return RouteInterface
     */
    public function prefix(string $prefix): RouteInterface;

    /**
     * Group http requests together
     * @param callable $closure
     * @return RouteInterface
     */
    public function group(callable $closure): RouteInterface;

    /** Set namespace to group of routes
     * @param string $namespace
     * @return RouteInterface
     */
    public function namespace(string $namespace): RouteInterface;

    /** Set namespace to group of routes
     * @param string $middleware
     * @return RouteInterface
     */
    public function middleware(string $middleware): RouteInterface;

    /** Set name to group of routes
     * @param string $name
     * @return  RouteInterface
     */
    public function name(string $name): RouteInterface;

    /**
     * Add field of data to route collection
     * @param string $name
     * @param mixed $value
     * @return RouteInterface
     */
    public function addField(string $name, $value): RouteInterface;

    /**
     * Append string of prefix to route
     * @param string $prefix
     * @return RouteInterface
     */
    public function append(string $prefix): RouteInterface;

    /**
     * Prepend string of prefix to route
     * @param string $prefix
     * @return RouteInterface
     */
    public function prepend(string $prefix): RouteInterface;

    /**
     * Add GET to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function get(string $route, $handler): RouteInterface;

    /**
     * Add POST to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function post(string $route, $handler): RouteInterface;

    /**
     * Add PUT to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function put(string $route, $handler): RouteInterface;

    /**
     * Add PATCH to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function patch(string $route, $handler): RouteInterface;

    /**
     * Add DELETE to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function delete(string $route, $handler): RouteInterface;

    /**
     * Add HEAD to route collection
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface
     */
    public function head(string $route, $handler): RouteInterface;

    /**
     * Retrieve registered routes
     * @return array<mixed>
     */
    public function getRouteData(): array;

    /**
     * Register route to nikita popov's router
     * @return RouteInterface
     */
    public function onRegister(): RouteInterface;
}