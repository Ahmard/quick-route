<?php


namespace QuickRoute\Route;


use QuickRoute\RouteInterface;

trait RequestMethods
{
    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function get(string $route, $handler): RouteInterface
    {
        return $this->add('GET', $route, $handler);
    }

    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function post(string $route, $handler): RouteInterface
    {
        return $this->add('POST', $route, $handler);
    }

    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function patch(string $route, $handler): RouteInterface
    {
        return $this->add('PATCH', $route, $handler);
    }

    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function put(string $route, $handler): RouteInterface
    {
        return $this->add('PUT', $route, $handler);
    }

    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function delete(string $route, $handler): RouteInterface
    {
        return $this->add('DELETE', $route, $handler);
    }

    /**
     * @param string $route
     * @param mixed $handler
     * @return RouteInterface $this
     */
    public function head(string $route, $handler): RouteInterface
    {
        return $this->add('HEAD', $route, $handler);
    }
}