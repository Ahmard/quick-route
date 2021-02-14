<?php


namespace QuickRoute\Router;


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
    public function get(string $route, $handler): RouteInterface
    {
        return $this->addRoute('GET', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function post(string $route, $handler): RouteInterface
    {
        return $this->addRoute('POST', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $route, $handler): RouteInterface
    {
        return $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function put(string $route, $handler): RouteInterface
    {
        return $this->addRoute('PUT', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $route, $handler): RouteInterface
    {
        return $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * @inheritDoc
     */
    public function head(string $route, $handler): RouteInterface
    {
        return $this->addRoute('HEAD', $route, $handler);
    }
}