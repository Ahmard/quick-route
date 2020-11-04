<?php


namespace QuickRoute\Route;


use QuickRoute\Route;

/**
 * Class Getter
 * @package QuickRoute\Route
 * @internal For internal use only
 */
class Getter
{
    private array $routes;

    private array $routeDefaultData = [];

    public static function create()
    {
        return new self();
    }

    /**
     * Retrieve routes
     * @param array $routes
     * @param array $defaultData
     * @return array
     */
    public function get(array $routes, array $defaultData = [])
    {
        $this->routeDefaultData = $defaultData;

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
                        $parentMiddleware = ($parentMiddleware ? $parentMiddleware . '|' : '') . $routeData['middleware'];
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

                    //If default data is passed
                    if (! empty($this->routeDefaultData)) {
                        $ready['prefix'] = $this->buildPrefix(($this->routeDefaultData['prefix'] ?? ''), $ready['prefix']);
                        $ready['namespace'] = ($this->routeDefaultData['namespace'] ?? '') . $ready['namespace'];
                        $ready['name'] = ($this->routeDefaultData['name'] ?? '') . $ready['name'];
                        if (isset($this->routeDefaultData['middleware'])) {
                            $ready['middleware'] = $this->routeDefaultData['middleware'] . ($ready['middleware'] ? '|' . $ready['middleware'] : '');
                        }
                    }else{
                        $ready['prefix'] = $this->removeRootSlash($ready['prefix']);
                    }

                    //We are now sure that all slashes at the prefix's beginning are cleaned
                    //Now let's put it back
                    $ready['prefix'] = '/' . $ready['prefix'];

                    //Clean prefix
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
            return ($prefix1 ? $prefix1 . '/' : '') . $prefix2;
        }

        return $prefix1;
    }

    /**
     * Remove slash at the end of prefix
     * @param string|null $prefix
     * @return string|null
     */
    private function removeTrailingSlash(?string $prefix)
    {
        $prefixLength = strlen($prefix) - 1;
        if ($prefixLength > 0 && $prefix[$prefixLength] == '/') {
            $prefix = $this->removeTrailingSlash(substr($prefix, 0, $prefixLength));
        }

        return $this->removeRootSlash($prefix);
    }

    /**
     * Remove slash at the beginning of prefix
     * @param string $prefix
     * @return string
     */
    private function removeRootSlash(string $prefix)
    {
        if (substr($prefix, 0, 1) == '/') {
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