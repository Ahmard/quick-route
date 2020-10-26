<?php

namespace QuickRoute;

use QuickRoute\Route\TheRoute;

/**
 * Command listeners registry
 * @package App\Core\Router
 * @method static TheRoute prefix(string $prefix)
 * @method static TheRoute name(string $name)
 * @method static TheRoute namespace(string $namespace)
 * @method static TheRoute middleware(string $middleware)
 * @method static TheRoute prepend(string $prefixToPrepend)
 * @method static TheRoute append(string $prefixToAppend)
 * @method static TheRoute group(callable $closure)
 * @method static TheRoute with(array $withDat)
 * @method static TheRoute get(string $route, $controller)
 * @method static TheRoute post(string $route, $controller)
 * @method static TheRoute put(string $route, $controller)
 * @method static TheRoute patch(string $route, $controller)
 * @method static TheRoute delete(string $route, $controller)
 */
class Route
{
    /**
     * @var TheRoute[]
     */
    protected static array $called = [];

    protected static TheRoute $theRouter;

    protected static array $defaultRouteConfig = [];

    /**
     * @param string $name
     * @param array $args
     * @return TheRoute
     */
    public static function __callStatic($name, $args)
    {
        self::$theRouter = new TheRoute(self::$defaultRouteConfig);

        return self::$theRouter->$name(...$args);
    }

    public static function use(array $theRoute)
    {
        self::$defaultRouteConfig = $theRoute;
    }

    /**
     * Get all registered routers
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$called;
    }

    public static function addRoute(TheRoute $route)
    {
        self::$called[] = $route;
    }
}
