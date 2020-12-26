<?php

namespace QuickRoute\Route;

class RouteData
{
    protected array $routeData;

    public function __construct(array $routeData)
    {
        $this->routeData = $routeData;
    }

    /**
     * Get route data as array
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->routeData;
    }

    /**
     * Get route prefix
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->routeData['prefix'] ?? '';

    }

    /**
     * Get route name
     * @return string
     */
    public function getName(): string
    {
        return $this->routeData['name'] ?? '';
    }

    /**
     * Get appended route prefix
     * @return string
     */
    public function getAppendedPrefix(): string
    {
        return $this->routeData['append'] ?? '';
    }

    /**
     * Get prepended route prefix
     * @return string
     */
    public function getPrependedPrefix(): string
    {
        return $this->routeData['prepend'] ?? '';
    }

    /**
     * Get route namespace
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->routeData['namespace'] ?? '';
    }

    /**
     * Get route method
     * @return string
     */
    public function getMethod(): string
    {
        return $this->routeData['method'] ?? '';
    }

    /**
     * Get route middleware
     * @return string
     */
    public function getMiddleware(): string
    {
        return $this->routeData['middleware'] ?? '';
    }

    /**
     * Get route handler
     * @return mixed|null
     */
    public function getHandler()
    {
        return $this->routeData['handler'] ?? null;
    }

    /**
     * Get route fields
     * @return array<mixed>
     */
    public function getFields(): array
    {
        return $this->routeData['fields'] ?? [];
    }
}