<?php


namespace QuickRoute\Route;


use QuickRoute\RouteInterface;

trait RequestMethods
{
    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function get(string $route, $controller): RouteInterface
    {
        return $this->add('GET', $route, $controller);
    }

    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function post(string $route, $controller): RouteInterface
    {
        return $this->add('POST', $route, $controller);
    }

    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function patch(string $route, $controller): RouteInterface
    {
        return $this->add('PATCH', $route, $controller);
    }

    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function put(string $route, $controller): RouteInterface
    {
        return $this->add('PUT', $route, $controller);
    }

    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function delete(string $route, $controller): RouteInterface
    {
        return $this->add('DELETE', $route, $controller);
    }

    /**
     * @param string $route
     * @param mixed $controller
     * @return RouteInterface $this
     */
    public function head(string $route, $controller): RouteInterface
    {
        return $this->add('HEAD', $route, $controller);
    }
}