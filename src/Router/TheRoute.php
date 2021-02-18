<?php

namespace QuickRoute\Router;

use Closure;
use JsonSerializable;
use QuickRoute\Route;
use QuickRoute\RouteInterface;

class TheRoute implements RouteInterface, JsonSerializable
{
    protected string $prefix = '';
    protected string $namespace = '';
    protected string $middleware = '';
    protected string $name = '';
    protected string $append = '';
    protected string $prepend = '';
    protected string $method = '';
    protected array $fields = [];
    protected Closure $group;
    protected TheRoute $parentRoute;

    /**
     * @var mixed Route handler/handler
     */
    protected $handler;


    public function __construct(?TheRoute $parentRoute = null)
    {
        if (null !== $parentRoute) {
            $this->parentRoute = $parentRoute;
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $route, $handler): RouteInterface
    {
        return $this->addRoute('GET', $route, $handler);
    }

    /**
     * Add route
     * @param string $method
     * @param string $route
     * @param mixed $handlerClass
     * @return $this
     */
    protected function addRoute(string $method, string $route, $handlerClass): RouteInterface
    {
        $this->method = $method;
        $this->prefix = $route;
        $this->handler = $handlerClass;
        return $this;
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

    /**
     * @inheritDoc
     */
    public function match($methods, string $uri, $handler): RouteInterface
    {
        if (is_array($methods)) {
            foreach ($methods as $method) {
                $method = strtolower($method);
                $route = new TheRoute($this);
                Route::push($route);
                $route->$method($uri, $handler);
            }

            return $this;
        }

        return $this->addRoute($methods, $uri, $handler);
    }

    /**
     * @inheritDoc
     */
    public function prefix(string $prefix): RouteInterface
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function append(string $prefix): RouteInterface
    {
        $this->append = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function prepend(string $prefix): RouteInterface
    {
        $this->prepend = $prefix;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function group(Closure $closure): RouteInterface
    {
        $this->group = $closure;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function namespace(string $namespace): RouteInterface
    {
        if ($namespace[strlen($namespace) - 1] !== "\\") {
            $namespace .= "\\";
        }
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(string $name): RouteInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function middleware(string $middleware): RouteInterface
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addField(string $name, $value): RouteInterface
    {
        $this->fields[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->getData();
    }

    public function getData(): array
    {
        $this->onRegister();

        $routeData = [
            'prefix' => $this->prefix,
            'namespace' => $this->namespace,
            'handler' => $this->handler,
            'middleware' => $this->middleware,
            'method' => $this->method,
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'group' => isset($this->group) ? $this->group : null,
            'fields' => $this->fields,
        ];

        if (isset($this->parentRoute)) {
            $routeData['parentRoute'] = $this->parentRoute;
        }

        return $routeData;
    }

    /**
     * @inheritDoc
     */
    public function onRegister(): RouteInterface
    {
        if (substr($this->prefix, 0, 1) != Getter::getDelimiter()) {
            $this->prefix = Getter::getDelimiter() . $this->prefix;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRouteData(): array
    {
        return $this->getData();
    }
}
