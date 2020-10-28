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
