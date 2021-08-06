<?php

namespace QuickRoute\Router;

use JsonSerializable;

class RouteData implements JsonSerializable
{
    protected array $routeData;

    public function __construct(array $routeData)
    {
        $this->routeData = $routeData;
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
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->routeData['middleware'];
    }

    /**
     * Get route handler
     *
     * @return mixed|null
     */
    public function getHandler()
    {
        return $this->routeData['handler'] ?? null;
    }

    /**
     * Get route controller, this method aliases getHandler()
     *
     * @return mixed|null
     */
    public function getController()
    {
        return $this->routeData['handler'] ?? null;
    }

    /**
     * Get route fields
     * @return array
     */
    public function getFields(): array
    {
        return $this->routeData['fields'] ?? [];
    }

    /**
     * Gets regular expressions associated with this route
     *
     * @return array
     */
    public function getRegExp(): array
    {
        return $this->routeData['parameterTypes'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getData();
    }

    /**
     * Get route data as array
     * @return array
     */
    public function getData(): array
    {
        return $this->routeData;
    }
}