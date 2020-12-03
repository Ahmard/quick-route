<?php


namespace QuickRoute\Route;


use FastRoute\Dispatcher as FastDispatcher;

class DispatchResult
{
    /**
     * @var mixed[]
     */
    private array $dispatchResult;

    /**
     * DispatchResult constructor.
     * @param string[] $dispatchResult
     */
    public function __construct(array $dispatchResult)
    {
        $this->dispatchResult = $dispatchResult;
    }


    /**
     * If url is found
     * @return bool
     */
    public function isFound()
    {
        return $this->dispatchResult[0] === FastDispatcher::FOUND;
    }

    /**
     * If url is not found
     * @return bool
     */
    public function isNotFound()
    {
        return $this->dispatchResult[0] === FastDispatcher::NOT_FOUND;
    }

    /**
     * If url method is not allowed
     * @return bool
     */
    public function isMethodNotAllowed()
    {
        return $this->dispatchResult[0] === FastDispatcher::METHOD_NOT_ALLOWED;
    }

    /**
     * Get dispatched url parameters
     * @return array|null
     */
    public function getUrlParameters()
    {
        return $this->dispatchResult[2] ?? null;
    }

    /**
     * Get found url class
     * @return RouteData
     */
    public function getRoute(): RouteData
    {
        return new RouteData($this->dispatchResult[1] ?? []);
    }
}