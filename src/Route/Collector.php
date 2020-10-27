<?php


namespace QuickRoute\Route;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as GCBDispatcher;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use QuickRoute\Route;

class Collector
{
    protected FastRouteCollector $collector;

    protected GCBDispatcher $dispatcher;

    protected array $collectedRoutes = [];

    public static function create()
    {
        return new self();
    }

    public function collectFile(string $filePath, array $routesInfo = [])
    {
        Route::use($routesInfo);
        require $filePath;
        $this->collectedRoutes += Route::getRoutes();
        return $this;
    }

    public function collect(array $routesInfo = [])
    {
        Route::use($routesInfo);
        $this->collectedRoutes += Route::getRoutes();
        return $this;
    }

    public function register()
    {
        foreach ($this->collectedRoutes as $route) {
            //Notify that we are about to register this route
            $route->onRegister();
            $routeData = $route->getRouteData();

            //Register route to Nikita's fast route.
            $this->getFastRouteCollector()->addRoute(strtoupper($routeData['method']), $routeData['prefix'], $route);
        }

        return $this;
    }

    /**
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
     * @return array
     */
    public function getCollectedRoutes(): array
    {
        return $this->collectedRoutes;
    }
}