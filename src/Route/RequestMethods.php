<?php


namespace QuickRoute\Route;


use QuickRoute\RouteInterface;

/**
 * Trait RequestMethods
 * @package QuickRoute\Route
 * @internal For internal use only
 */
trait RequestMethods
{
    /**
     * @inheritDoc
     */
    public function get(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('GET', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('POST', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('PUT', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function head(string $route, callable $handler): RouteInterface
    {
        return $this->addRoute('HEAD', $route, $handler);
    }
}