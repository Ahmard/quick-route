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
     * A file to save cache in
     * @var string
     */
    private string $cacheFile = '';

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
     * @param string $cacheFile
     * @return $this
     */
    public function cache(string $cacheFile): self
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }

    /**
     * Register routes to FastRoute
     * @return $this
     */
    public function register(): self
    {
        $this->doCollectRoutes();
        $rootFastCollector = $this->getFastRouteCollector(true);

        if (!empty($this->cachedRoutes)) {
            $this->fastRouteData = $this->cachedRoutes;
            return $this;
        }

        foreach ($this->collectedRoutes as $collectedRoute) {
            //Register to root collector
            $rootFastCollector->addRoute($collectedRoute['method'], $collectedRoute['prefix'], $collectedRoute);
        }

        $this->fastRouteData = $rootFastCollector->getData();

        if (empty($this->cachedRoutes) && '' != $this->cacheFile) {
            Cache::create($this->cacheFile, $this->fastRouteData);
        }

        return $this;
    }

    /**
     * Perform route collection
     * @return void
     */
    private function doCollectRoutes(): void
    {
        if (!$this->willCollect) {
            return;
        }

        if ('' != $this->cacheFile) {
            $cachedVersion = Cache::get($this->cacheFile);
        }

        if (!empty($cachedVersion)) {
            $this->cachedRoutes = $cachedVersion;
            return;
        }

        foreach ($this->collectableRoutes as $collectableRoute) {
            $collectableFile = $collectableRoute['file'] ?? null;
            if (isset($collectableFile)) {
                Route::restart();
                require $collectableFile;
                //Store collected routes
                $this->collectedRoutes = array_merge($this->collectedRoutes, Getter::create()->get(
                    Route::getRoutes(),
                    $collectableRoute['data']
                ));
            } else {
                $this->collectedRoutes = array_merge($this->collectedRoutes, Getter::create()->get(
                    Route::getRoutes(),
                    $collectableRoute['data']
                ));
            }
        }

        $this->willCollect = false;
    }

    /**
     * Get FastRoute's route collector
     * @param bool $createNew
     * @return FastRouteCollector
     */
    public function getFastRouteCollector(bool $createNew = false): FastRouteCollector
    {
        if (isset($this->collector)) {
            if ($createNew) {
                $this->collector = new FastRouteCollector(new Std(), new GroupCountBased());
            }
        } else {
            $this->collector = new FastRouteCollector(new Std(), new GroupCountBased());
        }

        return $this->collector;
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
     * Get computed route
     * @return array[]
     */
    public function getFastRouteData(): array
    {
        return $this->fastRouteData;
    }
}