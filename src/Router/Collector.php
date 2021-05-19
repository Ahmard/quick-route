<?php


namespace QuickRoute\Router;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use InvalidArgumentException;
use QuickRoute\Route;

class Collector
{
    private FastRouteCollector $collector;

    /**
     * List of routes to be collected
     *
     * @var array $collectableRoutes
     */
    private array $collectableRoutes = [];

    /**
     * List of collected routes
     *
     * @var array[] $collectedRoutes
     */
    private array $collectedRoutes = [];

    /**
     * A file to save cache in
     *
     * @var string $cacheFile
     */
    private string $cacheFile = '';

    /**
     * Indicates that routes has closures in their handles
     *
     * @var bool $routesHasClosures
     */
    private bool $routesHasClosures = true;

    /**
     * List of found cached routes
     *
     * @var array[] $cachedRoutes
     */
    private array $cachedRoutes = [];

    /**
     * A fast-route compatible route data
     *
     * @var array[] $fastRouteData
     */
    private array $fastRouteData = [];

    /**
     * An indicator whether cache will collected
     * This is important as to not re-collect routes by calling different methods that invoke doCollectRoute() method
     *
     * @var bool $willCollect
     */
    private bool $willCollect = false;

    /**
     * Indicates whether this collector has been registered or not
     * This will be useful to help avoid re-registering single collector
     *
     * @var bool $isRegistered
     */
    private bool $isRegistered = false;

    /**
     * Route prefix delimiter
     *
     * @var string $delimiter
     */
    private string $delimiter = '/';

    /**
     * Create an instance of collector
     *
     * @return Collector
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Collect routes defined in a file
     *
     * @param string $filePath
     * @param array $routesInfo
     * @return Collector
     */
    public function collectFile(string $filePath, array $routesInfo = []): Collector
    {
        $this->willCollect = true;
        $this->collectableRoutes[] = [
            'file' => $filePath,
            'data' => $routesInfo,
        ];

        return $this;
    }

    /**
     * Collect routes defined above or in included file
     *
     * @param array $routesInfo
     * @return Collector
     */
    public function collect(array $routesInfo = []): Collector
    {
        $this->willCollect = true;
        $this->collectableRoutes[] = [
            'data' => $routesInfo,
        ];

        return $this;
    }

    /**
     * Cache this group
     *
     * @param string $cacheFile Location to cache file
     * @param bool $hasClosures Indicates that routes has closures in their handlers.
     * This will tell caching lib to not look closures.
     * @return $this
     */
    public function cache(string $cacheFile, bool $hasClosures = true): self
    {
        $this->cacheFile = $cacheFile;
        $this->routesHasClosures = $hasClosures;

        return $this;
    }

    /**
     * Set custom route prefix delimiter
     *
     * @param string $delimiter
     * @return $this
     */
    public function prefixDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * Register routes to FastRoute
     *
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

        $this->isRegistered = true;

        return $this;
    }

    /**
     * Perform route collection
     *
     * @return void
     */
    private function doCollectRoutes(): void
    {
        if (!$this->willCollect) {
            return;
        }

        if ('' != $this->cacheFile) {
            $cachedVersion = Cache::get($this->cacheFile, $this->routesHasClosures);
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
            }
            $this->collectedRoutes = array_merge(
                $this->collectedRoutes,
                Getter::create()
                    ->prefixDelimiter($this->delimiter)
                    ->get(
                        Route::getRoutes(),
                        $collectableRoute['data']
                    )
            );
        }

        $this->willCollect = false;
    }

    /**
     * Get FastRoute's route collector
     *
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
     *
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
     *
     * @return array[]
     */
    public function getFastRouteData(): array
    {
        return $this->fastRouteData;
    }

    /**
     * Checks whether this collector has been registered
     *
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }


    /**
     * Find route by name
     *
     * @param string $routeName
     * @return RouteData|null
     */
    public function route(string $routeName): ?RouteData
    {
        $this->doCollectRoutes();
        foreach ($this->collectedRoutes as $collectedRoute) {
            if ($routeName == $collectedRoute['name']) {
                return new RouteData($collectedRoute);
            }
        }

        return null;
    }

    /**
     * Generate route http uri using route's name
     *
     * @param string $routeName
     * @param array $routeParams an array of [key => value] of route parameters
     * @return string|null
     */
    public function uri(string $routeName, array $routeParams = []): ?string
    {
        $foundRoute = $this->route($routeName);
        if (!$foundRoute) return null;

        $prefix = $foundRoute->getPrefix() ?? null;
        if (!$prefix) return null;

        return $this->replaceParamWithValue($prefix, $routeParams);
    }

    protected function replaceParamWithValue(string $prefix, array $params): string
    {
        preg_match_all("@{([0-9a-zA-Z]+):?.*?\+?}@", $prefix, $matchedParams);

        for ($i = 0; $i < count($matchedParams[0]); $i++) {
            $paramValue = $params[$matchedParams[1][$i]] ?? null;
            if (null == $paramValue) {
                throw new InvalidArgumentException("Missing route parameter value for \"{$matchedParams[1][$i]}\"");
            }
            $prefix = str_replace($matchedParams[0][$i], $params[$matchedParams[1][$i]], $prefix);
        }

        return $prefix;
    }

}