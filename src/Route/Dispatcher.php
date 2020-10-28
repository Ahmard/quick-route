<?php


namespace QuickRoute\Route;

use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased;

class Dispatcher
{
    private string $dispatcher;

    private Collector $collector;

    public static function create(Collector $collector)
    {
        return new self($collector);
    }

    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Dispatch url routing
     * @param string $method
     * @param string $path
     * @return DispatchResult
     */
    public function dispatch(string $method, string $path): DispatchResult
    {
        //Remove trailing forward slash
        $lengthPath = strlen($path) - 1;
        if ($lengthPath > 1 && $path[$lengthPath] == '/') {
            $path = substr($path, 0, $lengthPath);
        }

        $urlData = $this->createDispatcher()
            ->dispatch(strtoupper($method), $path);

        return new DispatchResult($urlData);
    }

    /**
     * Set your own dispatcher
     * @param FastDispatcher $dispatcher
     */
    public function setDispatcher(FastDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return FastDispatcher
     */
    private function createDispatcher()
    {
        if(! isset($this->dispatcher)){
            $this->dispatcher = GroupCountBased::class;
        }

        $dispatcher = $this->dispatcher;
        return (new $dispatcher($this->collector->getFastRouteCollector()->getData()));
    }
}