<?php


namespace QuickRoute\Route;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use QuickRoute\Route;

final class Collector
{
    private FastRouteCollector $collector;

    private array $collectedRoutes = [];

    private array $routes = [];

    private ?string $cacheFileDest;

    private ?string $cacheDictionary;

    private ?string $fileToCollect;

    private bool $shouldCollect = true;

    private array $routesInfo = [];

    private array $cachedRoutes = [];


    public static function create()
    {
        return new self();
    }

    /**
     * Collect routes in file
     * @param string $filePath
     * @param array $routesInfo
     * @return $this
     */
    public function collectFile(string $filePath, array $routesInfo = [])
    {
        $this->shouldCollect = true;
        $this->fileToCollect = $filePath;
        $this->routesInfo = $routesInfo;
        return $this;
    }

    /**
     * Collect routes
     * @param array $routesInfo
     * @return $this
     */
    public function collect(array $routesInfo = [])
    {
        $this->shouldCollect = true;
        $this->routesInfo = $routesInfo;
        return $this;
    }

    /**
     * Cache this group
     * @param string $cacheFileDest
     * @param string|null $cacheDictionary
     * @return $this
     */
    public function cache(string $cacheFileDest, ?string $cacheDictionary = null)
    {
        $this->cacheFileDest = $cacheFileDest;
        $this->cacheDictionary = $cacheDictionary;
        return $this;
    }

    private function doCollectRoutes()
    {
        if ($this->shouldCollect) {
            if (isset($this->fileToCollect)) {
                require $this->fileToCollect;
                $routes = Route::getRoutes();
                $this->collectedRoutes += $this->get($routes);
            } else {
                $routes = Route::getRoutes();
                $this->collectedRoutes += $this->get($routes);
            }

        }
    }

    /**
     * Register routes to FastRoute
     * @return $this
     */
    public function register()
    {
        if (isset($this->cacheFileDest) && isset($this->fileToCollect)) {
            //Set cache dictionary
            Cache::setCacheDictionaryFile($this->cacheDictionary);

            $fastRouteData = Cache::get($this->fileToCollect, $this->cacheFileDest);

            if (is_array($fastRouteData)) {
                $this->cachedRoutes += $fastRouteData;
            }
        }

        if (empty($fastRouteData)) {
            //Collect routes
            $this->doCollectRoutes();

            //Register route to Nikita's fast route.
            foreach ($this->collectedRoutes as $routeData) {
                $this->getFastRouteCollector()->addRoute(strtoupper($routeData['method']), $routeData['prefix'], $routeData);
            }

            //Since no version of current file is cached
            //Lets cache it now
            if (isset($this->cacheFileDest) && isset($this->fileToCollect)) {
                Cache::createCache($this->fileToCollect, $this->cacheFileDest, $this->getFastRouteCollector()->getData());

                //Reset file to collect
                $this->fileToCollect = null;
                $this->shouldCollect = false;
            }
        }

        return $this;
    }

    /**
     * Get collected routes, array of routes
     * @return array
     */
    public function getCollectedRoutes(): array
    {
        //Collect routes
        $this->doCollectRoutes();

        //Reset file to collect
        $this->fileToCollect = null;
        $this->shouldCollect = false;

        return $this->collectedRoutes;
    }

    /**
     * @return array
     */
    public function getCachedRoutes(): array
    {
        return $this->cachedRoutes;
    }

    /**
     * Get FastRoute's route collector
     * @return FastRouteCollector
     */
    public function getFastRouteCollector(): FastRouteCollector
    {
        if (!isset($this->collector)) {
            $this->collector = new FastRouteCollector(new Std(), new GroupCountBased());
        }

        return $this->collector;
    }

    /**
     * Retrieve routes
     * @param array $routes
     * @return array
     */
    private function get(array $routes)
    {
        $routes = $this->loop($routes);

        $this->build($routes);

        return $this->routes;
    }

    /**
     * Loop through routes
     * @param TheRoute[] $routes
     * @return array
     */
    private function loop(array $routes)
    {
        $results = [];

        for ($i = 0; $i < count($routes); $i++) {
            $route = $routes[$i]->getRouteData();
            $results[$i]['route'] = $route;
            if (!empty($route['group'])) {
                $results[$i]['children'] = $this->loop($this->getGroup($route['group']));
            }
        }

        $this->routes = [];

        return $results;
    }

    /**
     * Build route structure
     * @param array $routes
     * @param array $parent
     */
    private function build(array $routes, array $parent = [])
    {
        foreach ($routes as $route) {
            $routeData = $route['route'];
            if (isset($route['route'])) {

                if (isset($parent['prefix'])) {
                    $parentPrefix = $parent['prefix'];
                    if (!empty($routeData['prefix'])) {
                        $parentPrefix = $parentPrefix . ($routeData['prefix'] == '/' ? '' : $routeData['prefix']);
                    }
                }

                if (isset($parent['middleware'])) {
                    $parentMiddleware = $parent['middleware'];
                    if ($routeData['middleware']) {
                        $parentMiddleware = $parentMiddleware . '|' . $routeData['middleware'];
                    }
                }

                $data = [
                    'prefix' => ($parentPrefix ?? $routeData['prefix']),
                    'append' => $this->buildPrefix(
                        $this->getNullableString($routeData, 'append'),
                        $this->getNullableString($parent, 'append')
                    ),
                    'prepend' => $this->buildPrefix(
                        $this->getNullableString($parent, 'prepend'),
                        $this->getNullableString($routeData, 'prepend')
                    ),
                    'namespace' => $this->getNullableString($parent, 'namespace') . $routeData['namespace'],
                    'name' => $this->getNullableString($parent, 'name') . $routeData['name'],
                    'controller' => $routeData['controller'],
                    'method' => $routeData['method'],
                    'middleware' => ($parentMiddleware ?? $routeData['middleware']),
                ];

                if (!empty($routeData['method'])) {
                    $ready = $data;
                    $prefix = $this->buildPrefix($ready['prepend'], $ready['prefix']);
                    $prefix = $this->buildPrefix($prefix, $ready['append']);
                    $ready['prefix'] = $prefix;
                    if (isset($this->routesInfo)) {
                        $ready['prefix'] = $this->buildPrefix(($this->routesInfo['prefix'] ?? ''), $ready['prefix']);
                        $ready['namespace'] = ($this->routesInfo['namespace'] ?? '') . $ready['namespace'];
                        $ready['name'] = ($this->routesInfo['name'] ?? '') . $ready['name'];
                        if (isset($this->routesInfo['middleware'])) {
                            $ready['middleware'] = $this->routesInfo['middleware'] . '|' . $ready['middleware'];
                        }
                    }
                    $this->routes[] = $ready;
                }

                if (isset($route['children'])) {
                    $this->build($route['children'], $data);
                }

            }
        }
    }

    /**
     * Get routes grouped together
     * @param callable $callback
     * @return TheRoute[]
     */
    private function getGroup(callable $callback)
    {
        Route::restart();
        $callback();
        return Route::getRoutes();
    }

    /**
     * Carefully join two prefix together
     * @param string|null $prefix1
     * @param string|null $prefix2
     * @return string
     */
    private function buildPrefix(?string $prefix1, ?string $prefix2)
    {
        $prefix2 = $this->removeTrailingSlash($prefix2);
        if ($prefix2 && $prefix2 != '/') {
            return $prefix1 . '/' . $prefix2;
        }

        return empty($prefix1) ? '/' : $prefix1;
    }

    /**
     * Remove slash at the end of prefix
     * @param string|null $prefix
     * @return false|string|null
     */
    private function removeTrailingSlash(?string $prefix)
    {
        $totalStr = strlen($prefix) - 1;
        if ($totalStr > 0) {
            if ($prefix[$totalStr] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 0, $totalStr);
            }

            if ($prefix[0] == '/' && $totalStr != 0) {
                $prefix = substr($prefix, 1, $totalStr + 1);
            }
        }

        return $this->removeRootSlash($prefix);
    }
    
    private function removeRootSlash(string $prefix)
    {
        if(substr($prefix, 0, 1) == '/'){
            return $this->removeRootSlash(substr($prefix, 1, strlen($prefix)));
        }

        return $prefix;
    }

    /**
     * Retrieve string from an array
     * @param array $array
     * @param string $key
     * @return string
     */
    private function getNullableString(array $array, string $key)
    {
        return $array[$key] ?? '';
    }
}