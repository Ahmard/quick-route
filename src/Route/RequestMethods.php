<?php


namespace QuickRoute\Route;


use QuickRoute\RouteInterface;

trait RequestMethods
{
    public function get(string $route, $controller): RouteInterface
    {
        return $this->add('get', $route, $controller);
    }

    public function post(string $route, $controller): RouteInterface
    {
        return $this->add('post', $route, $controller);
    }

    public function patch(string $route, $controller): RouteInterface
    {
        return $this->add('patch', $route, $controller);
    }

    public function put(string $route, $controller): RouteInterface
    {
        return $this->add('put', $route, $controller);
    }

    public function delete(string $route, $controller): RouteInterface
    {
        return $this->add('delete', $route, $controller);
    }
}