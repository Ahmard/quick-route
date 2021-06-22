<?php


namespace QuickRoute\Router;

use Closure;
use function Opis\Closure\serialize;
use function Opis\Closure\unserialize;

/**
 * Class Cache
 * @package QuickRoute\Route
 * @internal for internal use only
 */
class Cache
{
    /**
     * Get file cached route
     * @param string $cacheFile File to save cache in
     * @param bool $hasClosures
     * @return array[]|null
     */
    public static function get(string $cacheFile, bool $hasClosures = true): ?array
    {
        if (file_exists($cacheFile)) {
            $cachedRoutes = require $cacheFile;

            if ($hasClosures) {
                foreach ($cachedRoutes as &$httpVerbs) {
                    foreach ($httpVerbs as &$verbRoutes) {
                        foreach ($verbRoutes as &$route) {
                            if (
                                isset($route['handler']) && is_string($route['handler'])
                                && '__closure__' == substr($route['handler'], 0, 11)
                            ) {
                                $route['handler'] = unserialize(substr($route['handler'], 11));
                            }
                        }
                    }
                }
            }

            return $cachedRoutes;
        }


        return null;
    }

    /**
     * Create cache
     * @param string $cacheFile File to save cache in
     * @param array[] $routes Array routes to cache
     * @param bool $hasClosures Indicates that the given routes has closures
     */
    public static function create(string $cacheFile, array $routes, bool $hasClosures = true): void
    {
        if ($hasClosures) {
            foreach ($routes as &$httpVerbs) {
                foreach ($httpVerbs as &$verbRoutes) {
                    foreach ($verbRoutes as &$route) {
                        if ($route['handler'] instanceof Closure) {
                            $route['handler'] = '__closure__' . serialize($route['handler']);
                        }
                    }
                }
            }
        }

        file_put_contents($cacheFile, '<?php return ' . var_export($routes, true) . ';');
    }
}