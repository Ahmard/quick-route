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
    /**
     * @var array[]
     */
    private array $routes;

    /**
     * @var mixed[]
     */
    private array $routeDefaultData = [];
    private static string $delimiter = '/';

    public static function create(): self
    {
        return new self();
    }

    /**
     * Set custom route prefix delimiter
     * @param string $delimiter
     * @return $this
     */
    public function prefixDelimiter(string $delimiter): self
    {
        self::$delimiter = $delimiter;
        return $this;
    }

    public static function getDelimiter(): string
    {
        return self::$delimiter;
    }

    /**
     * Retrieve routes
     * @param TheRoute[] $routes
     * @param array[] $defaultData
     * @return array[]
     */
    public function get(array $routes, array $defaultData = []): array
    {
        $this->routeDefaultData = $defaultData;

        $routes = $this->loop($routes);

        $this->build($routes);

        return $this->routes;
    }

    /**
     * Loop through routes
     * @param TheRoute[] $routes
     * @return array<mixed>
     */
    private function loop(array $routes): array
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
     * Get routes grouped together
     * @param callable $callback
     * @return TheRoute[]
     */
    private function getGroup(callable $callback): array
    {
        Route::restart();
        $callback();
        return Route::getRoutes();
    }

    /**
     * Build route structure
     * @param array<mixed> $routes
     * @param array<mixed> $parent
     */
    private function build(array $routes, array $parent = []): void
    {
        foreach ($routes as $route) {
            $routeData = $route['route'];
            if (isset($route['route'])) {

                if (isset($parent['prefix'])) {
                    $parentPrefix = $parent['prefix'];
                    if (!empty($routeData['prefix'])) {
                        $parentPrefix = $parentPrefix . ($routeData['prefix'] == self::$delimiter ? '' : $routeData['prefix']);
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
                    'handler' => $routeData['handler'],
                    'method' => $routeData['method'],
                    'middleware' => ($parentMiddleware ?? $routeData['middleware']),
                    'fields' => array_merge_recursive($parent['fields'] ?? [], $routeData['fields']),
                ];

                if (!empty($routeData['method'])) {
                    $ready = $data;
                    $prefix = $this->buildPrefix($ready['prepend'], $ready['prefix']);
                    $prefix = $this->buildPrefix($prefix, $ready['append']);
                    $ready['prefix'] = $prefix;

                    //If default data is passed
                    if (!empty($this->routeDefaultData)) {
                        $ready['prefix'] = $this->buildPrefix(($this->routeDefaultData['prefix'] ?? ''), $ready['prefix']);
                        $ready['namespace'] = ($this->routeDefaultData['namespace'] ?? '') . $ready['namespace'];
                        $ready['name'] = ($this->routeDefaultData['name'] ?? '') . $ready['name'];
                        if (isset($this->routeDefaultData['middleware'])) {
                            $ready['middleware'] = $this->routeDefaultData['middleware'] . ($ready['middleware'] ? '|' . $ready['middleware'] : '');
                        }
                    } else {
                        $ready['prefix'] = $this->removeRootDelimiter($ready['prefix']);
                    }

                    //We are now sure that all slashes at the prefix's beginning are cleaned
                    //Now let's put it back
                    $ready['prefix'] = self::$delimiter . $ready['prefix'];

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
     * Carefully join two prefix together
     * @param string $prefix1
     * @param string $prefix2
     * @return string
     */
    private function buildPrefix(string $prefix1, string $prefix2): string
    {
        $prefix2 = $this->removeTrailingDelimiter($prefix2);
        if ($prefix2 && $prefix2 != self::$delimiter) {
            return ($prefix1 ? $prefix1 . self::$delimiter : '') . $prefix2;
        }

        return $prefix1;
    }

    /**
     * Remove slash at the end of prefix
     * @param string $prefix
     * @return string
     */
    private function removeTrailingDelimiter(string $prefix): string
    {
        $prefixLength = strlen($prefix) - 1;
        if ($prefixLength > 0 && $prefix[$prefixLength] == self::$delimiter) {
            $prefix = $this->removeTrailingDelimiter(substr($prefix, 0, $prefixLength));
        }

        return $this->removeRootDelimiter($prefix);
    }

    /**
     * Remove slash at the beginning of prefix
     * @param string $prefix
     * @return string
     */
    private function removeRootDelimiter(string $prefix): string
    {
        if (substr($prefix, 0, 1) == self::$delimiter) {
            return $this->removeRootDelimiter(substr($prefix, 1, strlen($prefix)));
        }

        return $prefix;
    }

    /**
     * Retrieve string from an array
     * @param string[] $array
     * @param string $key
     * @return string
     */
    private function getNullableString(array $array, string $key): string
    {
        return $array[$key] ?? '';
    }
}