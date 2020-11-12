<?php


namespace QuickRoute\Route;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use QuickRoute\Route;

class Collector
{
    private FastRouteCollector $collector;

    /**
     * List of routes to be collected
     * @var mixed[]
     */
    private array $collectableRoutes = [];

    /**
     * List of collected routes
     * @var array[]
     */
    private array $collectedRoutes = [];

    /**
     * A directory where cache will be saved
     * @var string
     */
    private string $cacheDirectory = '';

    /**
     * A json file that will holds cache definitions
     * @var string
     */
    private string $cacheDefinitionFile = '';

    /**
     * List of found cached routes
     * @var array[]
     */
    private array $cachedRoutes = [];

    /**
     * A fast-route compatible route data
     * @var array[]
     */
    private array $fastRouteData = [];

    /**
     * An indicator whether cache will collected
     * This is important as to not re-collect routes by calling different methods that invoke doCollectRoute() method
     * @var bool
     */
    private bool $willCollect = false;


    /**
     * Create an instance of collector
     * @return Collector
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Collect routes in file
     * @param string $filePath
     * @param mixed[] $routesInfo
     * @return $this
     */
    public function collectFile(string $filePath, array $routesInfo = []): self
    {
        $this->willCollect = true;
        $this->collectableRoutes[] = [
            'file' => $filePath,
            'data' => $routesInfo,
        ];

        return $this;
    }

    /**
     * Collect routes
     * @param mixed[] $routesInfo
     * @return $this
     */
    public function collect(array $routesInfo = []): self
    {
        $this->willCollect = true;
        $this->collectableRoutes[] = [
            'data' => $routesInfo,
        ];

        return $this;
    }

    /**
     * Cache this group
     * @param string $cacheDirectory
     * @param string $cacheDefinitionFile
     * @return $this
     */
    public function cache(string $cacheDirectory, string $cacheDefinitionFile = ''): self
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->cacheDefinitionFile = $cacheDefinitionFile;
        return $this;
    }

    /**
     * Perform route collection
     * @return void
     */
    private function doCollectRoutes(): void
    {
        if(! $this->willCollect){
            return;
        }

        Cache::setCacheDefinitionFile($this->cacheDefinitionFile);

        foreach ($this->collectableRoutes as $collectableRoute) {
            $collectableFile = $collectableRoute['file'] ?? null;
            if (isset($collectableFile)) {
                $cachedVersion = null;
                $cacheName = $collectableFile;

                if ('' != $this->cacheDirectory) {
                    $cachedVersion = Cache::get($collectableFile, $this->cacheDirectory);
                }

                //No cache found, add to collected
                if (empty($cachedVersion)) {
                    Route::restart();
                    require $collectableFile;
                    //Store collected routes
                    $this->collectedRoutes[$cacheName] = Getter::create()->get(
                        Route::getRoutes(),
                        $collectableRoute['data']
                    );
                } //Cache found, add to cached
                else {
                    $this->cachedRoutes[$cacheName] = $cachedVersion;
                }
            } else {
                $this->collectedRoutes['__regular__'] = Getter::create()->get(
                    Route::getRoutes(),
                    $collectableRoute['data']
                );
            }
        }

        $this->willCollect = false;
    }

    /**
     * Register routes to FastRoute
     * @return $this
     */
    public function register(): self
    {
        $this->doCollectRoutes();
        $hasCollectedRoutes = false;
        $rootFastCollector = $this->getFastRouteCollector(true);

        foreach ($this->collectedRoutes as $collectableFile => $collectedRoutes) {

            $hasCollectedRoutes = true;

            $hasCache = function () use ($collectableFile){
                return(
                    '__regular__' !== $collectableFile
                    && is_string($collectableFile)
                    && '' !== $this->cacheDirectory
                );
            };

            /**
             * Instantiate cache collector
             * @var FastRouteCollector $cacheFastCollector
             */
            $cacheFastCollector = null;
            if ($hasCache()){
                $cacheFastCollector = $this->getFastRouteCollector(true);
            }

            foreach ($collectedRoutes as $collectedRoute){
                //Register to root collector
                $rootFastCollector->addRoute($collectedRoute['method'], $collectedRoute['prefix'], $collectedRoute);

                //Register to cache collector
                if ($hasCache()){
                    $cacheFastCollector->addRoute($collectedRoute['method'], $collectedRoute['prefix'], $collectedRoute);
                }
            }

            //Save cache, if cache is enabled and cache is loaded from file
            if ($hasCache()){
                Cache::create($collectableFile, $this->cacheDirectory, $cacheFastCollector->getData());
            }
        }

        if ($hasCollectedRoutes){
            $this->fastRouteData = $rootFastCollector->getData();
        }

        foreach ($this->cachedRoutes as $name => $cachedRoutes){
            $this->fastRouteData = array_merge_recursive($this->fastRouteData, $cachedRoutes);
        }

        return $this;
    }

    /**
     * Get collected routes, array of routes
     * @return array[]
     */
    public function getCollectedRoutes(): array
    {
        $this->doCollectRoutes();
        return $this->collectedRoutes;
    }

    /**
     * @return array[]
     */
    public function getCachedRoutes(): array
    {
        $this->doCollectRoutes();
        return $this->cachedRoutes;
    }

    /**
     * Get FastRoute's route collector
     * @param bool $createNew
     * @return FastRouteCollector
     */
    public function getFastRouteCollector(bool $createNew = false): FastRouteCollector
    {
        if (isset($this->collector)){
            if ($createNew){
                $this->collector = new FastRouteCollector(new Std(), new GroupCountBased());
            }
        }else {
            $this->collector = new FastRouteCollector(new Std(), new GroupCountBased());
        }

        return $this->collector;
    }

    /**
     * Get computed route
     * @return array[]
     */
    public function getFastRouteData(): array
    {
        return $this->fastRouteData;
    }
}