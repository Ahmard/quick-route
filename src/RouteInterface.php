<?php


namespace QuickRoute;

use Closure;

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
     * @param Closure $closure
     * @return RouteInterface
     */
    public function group(Closure $closure): RouteInterface;

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
     * @param callable|mixed $value
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
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function get(string $route, $handler): RouteInterface;

    /**
     * Add POST to route collection
     * @param string $route
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function post(string $route, $handler): RouteInterface;

    /**
     * Add PUT to route collection
     * @param string $route
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function put(string $route, $handler): RouteInterface;

    /**
     * Add PATCH to route collection
     * @param string $route
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function patch(string $route, $handler): RouteInterface;

    /**
     * Add DELETE to route collection
     * @param string $route
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function delete(string $route, $handler): RouteInterface;

    /**
     * Add HEAD to route collection
     * @param string $route
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function head(string $route, $handler): RouteInterface;

    /**
     * Register route to multiple http verbs
     * @param array|string $methods
     * @param string $uri
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function match($methods, string $uri, $handler): RouteInterface;

    /**
     * Register multiple paths to single handler
     *
     * @param string|array $paths
     * @param string $method
     * @param callable|mixed $handler
     * @return RouteInterface
     */
    public function any($paths, string $method, $handler): RouteInterface;

    /**
     * This single route declaration creates multiple routes to handle a variety of actions on the resource.
     *
     * @param string $uri
     * @param string $controller
     * @param string $idParameterName Specifies what name should be given to generated id parameter
     * @param bool $integerId Specifies that id associated with this route must be integer
     * @return RouteInterface
     */
    public function resource(
        string $uri,
        string $controller,
        string $idParameterName = 'id',
        bool $integerId = true
    ): RouteInterface;

    /**
     * Manually provide regular expression pattern to parameter
     *
     * @param string|array $parameter
     * @param string|null $regExp
     * @return RouteInterface
     */
    public function where($parameter, ?string $regExp = null): RouteInterface;

    /**
     * Indicates that the given route parameters must be number.
     *
     * @param string $parameterName
     * @return RouteInterface
     */
    public function whereNumber(string $parameterName): RouteInterface;

    /**
     * Indicates that the given route parameters must be alphabetic.
     *
     * @param string $parameterName
     * @return RouteInterface
     */
    public function whereAlpha(string $parameterName): RouteInterface;

    /**
     * Indicates that the given route parameters must be alphanumeric.
     *
     * @param string $parameterName
     * @return RouteInterface
     */
    public function whereAlphaNumeric(string $parameterName): RouteInterface;

    /**
     * Retrieve registered routes
     * @return array
     */
    public function getRouteData(): array;

    /**
     * This method will be invoked just before registering routes
     * @return RouteInterface
     */
    public function onRegister(): RouteInterface;
}