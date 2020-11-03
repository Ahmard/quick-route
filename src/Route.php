<?php

namespace QuickRoute;

use QuickRoute\Route\TheRoute;

/**
 * Command listeners registry
 * @package App\Core\Router
 * @method static TheRoute prefix(string $prefix) String of prefix
 * @method static TheRoute append(string $prefix) Append prefix to route
 * @method static TheRoute prepend(string $prefix) Prepend prefix to route
 * @method static TheRoute name(string $name) Name this route
 * @method static TheRoute namespace(string $namespace) Route namespace
 * @method static TheRoute middleware(string $middleware) Route middleware
 * @method static TheRoute group(callable $closure) Group routes together
 * @method static TheRoute get(string $route, $controller) Register this route as GET
 * @method static TheRoute post(string $route, $controller) Register this route as POST
 * @method static TheRoute put(string $route, $controller) Register this route as PUT
 * @method static TheRoute patch(string $route, $controller) Register this route as PATCH
 * @method static TheRoute delete(string $route, $controller) Register this route as DELETE
 */
class Route
{
    /**
     * @var TheRoute[]
     */
    protected static array $called = [];

    /**
     * @param string $name
     * @param array $args
     * @return TheRoute
     */
    public static function __callStatic(string $name, array $args)
    {
        $router = new TheRoute();
        self::$called[] = $router;
        return $router->$name(...$args);
    }

    /**
     * Clear previously collected routes
     * @return void
     */
    public static function restart()
    {
        self::$called = [];
    }

    /**
     * Get all registered routers
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$called;
    }
}
