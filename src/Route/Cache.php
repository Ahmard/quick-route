<?php


namespace QuickRoute\Route;

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
     * @return array[]|null
     */
    public static function get(string $cacheFile): ?array
    {
        if (file_exists($cacheFile)) {
            return require $cacheFile;
        }

        return null;
    }

    /**
     * Create cache
     * @param string $cacheFile File to save cache in
     * @param array[] $routes Array routes to cache
     */
    public static function create(string $cacheFile, array $routes): void
    {
        file_put_contents($cacheFile, '<?php return ' . var_export($routes, true) . ';');
    }
}