<?php

namespace QuickRoute;

use QuickRoute\Router\TheRoute;

/**
 * Http routes registry
 * @package QuickRoute
 * @method static TheRoute prefix(string $prefix) String of prefix
 * @method static TheRoute append(string $prefix) Append prefix to route
 * @method static TheRoute prepend(string $prefix) Prepend prefix to route
 * @method static TheRoute name(string $name) Name this route
 * @method static TheRoute namespace(string $namespace) Route namespace
 * @method static TheRoute middleware(string $middleware) Route middleware
 * @method static TheRoute group(callable $closure) Group routes together
 * @method static TheRoute get(string $uri, callable|mixed $handler) Register this route as GET
 * @method static TheRoute post(string $uri, callable|mixed $handler) Register this route as POST
 * @method static TheRoute put(string $uri, callable|mixed $handler) Register this route as PUT
 * @method static TheRoute patch(string $uri, callable|mixed $handler) Register this route as PATCH
 * @method static TheRoute delete(string $uri, callable|mixed $handler) Register this route as DELETE
 * @method static TheRoute head(string $uri, callable|mixed $handler) Register route to multiple http verbs
 * @method static TheRoute match(array|string $methods, string $uri, callable|mixed $handler) Register this route as HEAD
 * @method static TheRoute any(array|string $paths, string $method, callable|mixed $handler) Register multiple paths to single handler
 * @method static TheRoute addField(string $name, callable|mixed $handler) Add field of data route collection
 */
class Route
{
    /**
     * @var TheRoute[]
     */
    protected static array $called = [];


    /**
     * @param string $name
     * @param string[] $args
     * @return TheRoute
     */
    public static function __callStatic(string $name, array $args): TheRoute
    {
        $route = new TheRoute();
        //Recording match routes will cause registering an empty route
        if ('match' != $name) {
            self::push($route);
        }

        return $route->$name(...$args);
    }

    /**
     * Create fresh router
     * @return TheRoute
     */
    public static function create(): TheRoute
    {
        $route = new TheRoute();
        self::push($route);
        return $route;
    }

    /**
     * Add route class to list of routes
     * @param TheRoute $route
     */
    public static function push(TheRoute $route): void
    {
        self::$called[] = $route;
    }

    /**
     * Clear previously collected routes
     */
    public static function restart(): void
    {
        self::$called = [];
    }

    /**
     * Get all registered routers
     * @return TheRoute[]
     */
    public static function getRoutes(): array
    {
        return self::$called;
    }
}