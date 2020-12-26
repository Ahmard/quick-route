<?php


namespace QuickRoute\Route;

use FastRoute\Dispatcher as FastDispatcher;
use FastRoute\Dispatcher\GroupCountBased;

class Dispatcher
{
    private string $dispatcher;

    private Collector $collector;

    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    public static function create(Collector $collector): self
    {
        return new self($collector);
    }

    /**
     * Dispatch url routing
     * @param string $method Route method - It will be converted to uppercase
     * @param string $path Route url path - All data passed to url parameter after "?" will be discarded
     * @return DispatchResult
     */
    public function dispatch(string $method, string $path): DispatchResult
    {
        $lengthPath = strlen($path) - 1;

        //Make url convertible
        if (false !== $pos = strpos($path, '?')) {
            $path = substr($path, 0, $pos);
        }
        $path = rawurldecode($path);

        //Remove trailing forward slash
        if ($lengthPath > 0 && $path[$lengthPath] == Getter::getDelimiter()) {
            $path = substr($path, 0, $lengthPath);
        }

        if (substr($path, 0, 1) != Getter::getDelimiter()) {
            $path = Getter::getDelimiter() . $path;
        }

        $urlData = $this->createDispatcher()
            ->dispatch(strtoupper($method), $path);

        return new DispatchResult($urlData);
    }

    /**
     * @return FastDispatcher
     */
    private function createDispatcher(): FastDispatcher
    {
        if (!isset($this->dispatcher)) {
            $this->dispatcher = GroupCountBased::class;
        }

        $dispatcher = $this->dispatcher;
        $routeData = $this->collector->getFastRouteData();

        return (new $dispatcher($routeData));
    }

    /**
     * Set your own dispatcher
     * @param string $dispatcher A class namespace implementing \FastRoute\Dispatcher
     */
    public function setDispatcher(string $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }
}