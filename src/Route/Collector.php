<?php


namespace QuickRoute\Route;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use QuickRoute\Route;

class Collector
{
    private FastRouteCollector $collector;

    private array $collectedRoutes = [];

    private ?string $cacheFileDest;

    private ?string $cacheDictionary;

    private array $cachedRoutes = [];

    private array $routesToCollect = [];


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
        $this->routesToCollect[] = [
            'file' => $filePath,
            'data' => $routesInfo,
        ];

        return $this;
    }

    /**
     * Collect routes
     * @param array $routesInfo
     * @return $this
     */
    public function collect(array $routesInfo = [])
    {
        $this->routesToCollect[] = [
            'data' => $routesInfo,
        ];

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
        //Clear previously collected routes
        $this->collectedRoutes = [];

        foreach ($this->routesToCollect as $routeToCollect){
            if (isset($routeToCollect['file'])) {
                //Clear previously collected routes
                Route::restart();
                //Collect routes in file
                require $routeToCollect['file'];
                $this->collectedRoutes = array_merge(
                    $this->collectedRoutes,
                    Getter::create()->get(Route::getRoutes(), $routeToCollect['data'])
                );
            } else {
                //Collect defined
                $this->collectedRoutes = array_merge(
                    $this->collectedRoutes,
                    Getter::create()->get(Route::getRoutes(), $routeToCollect['data'])
                );
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

}