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

    private array $routes = [];


    public static function create()
    {
        return new self();
    }

    public function collectFile(string $filePath, array $routesInfo = [])
    {
        require $filePath;
        $routes = Route::getRoutes();
        $this->collectedRoutes += $this->get($routes);
        return $this;
    }

    public function collect(array $routesInfo = [])
    {
        $routes = Route::getRoutes();
        $this->collectedRoutes += $this->get($routes);
        return $this;
    }

    public function register()
    {
        foreach ($this->collectedRoutes as $routeData) {
            //Register route to Nikita's fast route.
            $this->getFastRouteCollector()->addRoute(strtoupper($routeData['method']), $routeData['prefix'], $routeData);
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

    private function get(array $routes)
    {
        $routes = $this->loop($routes);

        $this->build($routes);

        return $this->routes;
    }

    /**
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

    private function build(array $routes, array $parent = [])
    {
        foreach ($routes as $route) {
            $routeData = $route['route'];
            if (isset($route['route'])) {

                if (isset($parent['prefix'])) {
                    $parentPrefix = $parent['prefix'];
                    if (! empty($routeData['prefix'])) {
                        $parentPrefix = $parentPrefix . $routeData['prefix'];
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
                    'namespace' => ($parent['namespace'] ?? '') . $routeData['namespace'],
                    'name' => ($parent['name'] ?? '') . $route['route']['name'],
                    'controller' => $routeData['controller'],
                    'method' => $routeData['method'],
                    'middleware' => ($parentMiddleware ?? $routeData['middleware']),
                ];

                if (!empty($routeData['method'])) {
                    $this->routes[] = $data;
                }

                if (isset($route['children'])) {
                    $this->build($route['children'], $data);
                }
            }
        }
    }

    private function getGroup(callable $callback)
    {
        Route::restart();
        $callback();
        return Route::getRoutes();
    }
}