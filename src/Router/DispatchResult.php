<?php


namespace QuickRoute\Router;


use FastRoute\Dispatcher as FastDispatcher;

class DispatchResult
{
    /**
     * @var array
     */
    private array $dispatchResult;

    private Collector $collector;

    /**
     * DispatchResult constructor.
     *
     * @param string[] $dispatchResult
     * @param Collector $collector
     */
    public function __construct(array $dispatchResult, Collector $collector)
    {
        $this->dispatchResult = $dispatchResult;
        $this->collector = $collector;
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
     * Get all collected routes
     *
     * @return Collector
     */
    public function getCollector(): Collector
    {
        return $this->collector;
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