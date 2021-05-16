<?php


namespace QuickRoute\Router;

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

    /**
     * Collect routes defined above or in included file
     *
     * @param array $routesInfo
     * @return Dispatcher
     */
    public static function collectRoutes(array $routesInfo = []): Dispatcher
    {
        return self::create(Collector::create()->collect($routesInfo));
    }

    /**
     * Creates dispatcher instance
     *
     * @param Collector $collector
     * @return Dispatcher
     */
    public static function create(Collector $collector): Dispatcher
    {
        return new self($collector);
    }

    /**
     * Collect routes defined in a file
     *
     * @param string $filePath
     * @param array $routesInfo
     * @return Dispatcher
     */
    public static function collectRoutesFile(string $filePath, array $routesInfo = []): Dispatcher
    {
        return self::create(Collector::create()->collectFile($filePath, $routesInfo));
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

        //invalid & in url at ? position
        if (false !== $pos = strpos($path, '&')) {
            $path = substr($path, 0, $pos);
        }

        $path = str_replace('//', '/', $path);
        $path = rawurldecode($path);

        //Remove trailing forward slash
        if (($lengthPath > 0) && substr($path, $lengthPath, 1) == Getter::getDelimiter()) {
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

        //Register collector if it is not registered
        if (!$this->collector->isRegistered()) {
            $this->collector->register();
        }

        $dispatcher = $this->dispatcher;
        $routeData = $this->collector->getFastRouteData();

        return (new $dispatcher($routeData));
    }

    /**
     * Set your own dispatcher
     * @param string $dispatcher A class namespace implementing \FastRoute\Dispatcher
     * @return Dispatcher
     */
    public function setDispatcher(string $dispatcher): Dispatcher
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }
}