<?php


namespace QuickRoute\Route;


class Cache
{
    private static string $cacheDefinitionFile = 'cache-dict.json';

    /**
     * @param string $cacheDefinitionFile A dictionary file that track of cache will be saved to
     */
    public static function setCacheDefinitionFile(string $cacheDefinitionFile): void
    {
        self::$cacheDefinitionFile = $cacheDefinitionFile;
    }

    /**
     * Get file cached route
     * @param string $collectableRouteFile
     * @param string $cacheDirectory
     * @return array[]|null
     */
    public static function get(string $collectableRouteFile, string $cacheDirectory): ?array
    {
        $cacheDict = static::getCacheDictionary();

        if (isset($cacheDict[$collectableRouteFile])) {
            $stat = stat($collectableRouteFile);

            //If cache is not fresh
            if ( false !== $stat && $stat[9] !== $cacheDict[$collectableRouteFile]) {
                return null;
            }

            $cacheFile = self::generateCacheableName($cacheDirectory, $collectableRouteFile);
            if (file_exists($cacheFile)) {
                return require $cacheFile;
            }
        }

        return null;
    }

    /**
     * Create cache
     * @param string $collectableRouteFile Route file that will be cached
     * @param string $cacheDirectory File that this cache will be saved to
     * @param array[] $routes Array routes to cache
     */
    public static function create(string $collectableRouteFile, string $cacheDirectory, array $routes): void
    {
        $cacheDefinition = self::getCacheDictionary();
        $cacheFile = self::generateCacheableName($cacheDirectory, $collectableRouteFile);

        file_put_contents($cacheFile, '<?php return ' . var_export($routes, true) . ';');

        //Log
        $stat = stat($collectableRouteFile);
        if (false !== $stat){
            $cacheDefinition[$collectableRouteFile] = $stat[9];
        }


        file_put_contents(static::$cacheDefinitionFile, json_encode($cacheDefinition));
    }

    /**
     * Create name that can be used as cache name
     * @param string $cacheDirectory
     * @param string $collectableFile
     * @return string
     */
    public static function generateCacheableName(string $cacheDirectory, string $collectableFile): string
    {
        $cacheFile = explode('/', $collectableFile);
        return $cacheDirectory . DIRECTORY_SEPARATOR . end($cacheFile);
    }

    /**
     * Get defined cache dictionary
     * @return mixed[]
     */
    private static function getCacheDictionary()
    {
        if (!file_exists(self::$cacheDefinitionFile)) {
            touch(self::$cacheDefinitionFile);
        }

        return (array)json_decode(file_get_contents(self::$cacheDefinitionFile) ?: '');
    }
}