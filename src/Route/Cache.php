<?php


namespace QuickRoute\Route;


class Cache
{
    private static string $cacheDictFile = 'cache-dict.json';

    /**
     * @param string $cacheDictionary A dictionary file that track of cache will be saved to
     */
    public static function setCacheDictionaryFile(string $cacheDictionary): void
    {
        self::$cacheDictFile = $cacheDictionary;
    }

    public static function get(string $fileToCache, string $cacheLocation)
    {
        $cacheDict = static::getCacheDictionary();

        if (isset($cacheDict[$fileToCache])) {
            $stat = stat($fileToCache);
            //If cache is not fresh
            if ($stat['mtime'] !== $cacheDict[$fileToCache]) {
                return null;
            }

            if (file_exists($cacheLocation)) {
                return require $cacheLocation;
            }
        }

        return null;
    }

    /**
     * @param string $fileToCache Route file that will be cached
     * @param string $cacheLocation File that this cache will be saved to
     * @param array $routes Array routes to cache
     */
    public static function createCache(string $fileToCache, string $cacheLocation, array $routes)
    {
        $cacheDict = self::getCacheDictionary();
        file_put_contents($cacheLocation, '<?php return ' . var_export($routes, true) . ';');

        //Log
        $lastModified = stat($fileToCache)['mtime'];

        $cacheDict[$fileToCache] = $lastModified;

        file_put_contents(static::$cacheDictFile, json_encode($cacheDict));
    }

    private static function getCacheDictionary()
    {
        if (!file_exists(self::$cacheDictFile)) {
            touch(self::$cacheDictFile);
        }

        return (array)json_decode(file_get_contents(self::$cacheDictFile));
    }
}