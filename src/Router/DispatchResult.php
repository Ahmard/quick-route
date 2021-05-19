<?php


namespace QuickRoute\Router;


use FastRoute\Dispatcher as FastDispatcher;
use InvalidArgumentException;

class DispatchResult
{
    /**
     * @var array
     */
    private array $dispatchResult;

    private array $collectedRoutes;

    /**
     * DispatchResult constructor.
     *
     * @param string[] $dispatchResult
     */
    public function __construct(array $dispatchResult, array $collectedRoutes)
    {
        $this->dispatchResult = $dispatchResult;
        $this->collectedRoutes = $collectedRoutes;
    }


    /**
     * If url is found
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::FOUND;
    }

    /**
     * If url is not found
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::NOT_FOUND;
    }

    /**
     * If url method is not allowed
     *
     * @return bool
     */
    public function isMethodNotAllowed(): bool
    {
        return $this->dispatchResult[0] === FastDispatcher::METHOD_NOT_ALLOWED;
    }

    /**
     * Get dispatched url parameters
     * @return array|null
     */
    public function getUrlParameters(): ?array
    {
        return $this->dispatchResult[2] ?? null;
    }

    /**
     * Find route by name
     *
     * @param string $routeName
     * @return array|null
     */
    public function route(string $routeName): ?array
    {
        foreach ($this->collectedRoutes as $collectedRoute) {
            if ($routeName == $collectedRoute['name']) {
                return $collectedRoute;
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

        $prefix = $foundRoute['prefix'] ?? null;
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

    /**
     * Get all collected routes
     *
     * @return array
     */
    public function getCollectedRoutes(): array
    {
        return $this->collectedRoutes;
    }

    /**
     * Get found url
     *
     * @return RouteData
     */
    public function getRoute(): RouteData
    {
        return new RouteData($this->dispatchResult[1] ?? []);
    }
}