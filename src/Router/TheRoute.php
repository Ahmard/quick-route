<?php

namespace QuickRoute\Router;

use Closure;
use JsonSerializable;
use QuickRoute\Route;
use QuickRoute\RouteInterface;
use ValueError;

class TheRoute implements RouteInterface, JsonSerializable
{
    protected string $prefix = '';
    protected string $namespace = '';
    protected array $middlewares = [];
    protected string $name = '';
    protected string $append = '';
    protected string $prepend = '';
    protected string $method = '';
    protected array $fields = [];
    protected array $parameterTypes = [
        'number' => [],
        'alpha' => [],
        'alphanumeric' => [],
        'regExp' => [],
    ];
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
    public function head(string $route, $handler): RouteInterface
    {
        return $this->addRoute('HEAD', $route, $handler);
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
    public function any($paths, string $method, $handler): RouteInterface
    {
        if (is_array($paths)) {
            foreach ($paths as $path) {
                $method = strtolower($method);
                $route = new TheRoute($this);
                Route::push($route);
                $route->$method($path, $handler);
            }

            return $this;
        }

        return $this->addRoute($method, $paths, $handler);
    }

    /**
     * @inheritDoc
     */
    public function resource(
        string $uri,
        string $controller,
        string $idParameterName = 'id',
        bool $integerId = true
    ): RouteInterface
    {
        $id = $integerId
            ? '{' . "$idParameterName:[0-9]+" . '}'
            : '{' . "$idParameterName" . '}';

        //  GET /whatever
        $route = new TheRoute($this);
        $route->get($uri, [$controller, 'index'])->name("{$uri}.index");
        Route::push($route);

        //  GET /whatever/create
        $route = new TheRoute($this);
        $route->get("{$uri}/create", [$controller, 'create'])->name("{$uri}.create");
        Route::push($route);

        //  POST /whatever/create
        $route = new TheRoute($this);
        $route->post("{$uri}", [$controller, 'store'])->name("{$uri}.store");
        Route::push($route);

        //  GET /whatever/{$id}
        $route = new TheRoute($this);
        $route->get("{$uri}/{$id}", [$controller, 'show'])->name("{$uri}.show");
        Route::push($route);

        //  GET /whatever/{$id}/edit
        $route = new TheRoute($this);
        $route->get("{$uri}/{$id}/edit", [$controller, 'edit'])->name("{$uri}.edit");
        Route::push($route);

        //  PUT /whatever/{$id}
        $route = new TheRoute($this);
        $route->put("{$uri}/{$id}", [$controller, 'update'])->name("{$uri}.update");
        Route::push($route);

        //  PATCH /whatever/{$id}
        $route = new TheRoute($this);
        $route->patch("{$uri}/{$id}", [$controller, 'update'])->name("{$uri}.update");
        Route::push($route);

        //  DELETE /whatever/{$id}
        $route = new TheRoute($this);
        $route->delete("{$uri}/{$id}", [$controller, 'destroy'])->name("{$uri}.destroy");
        Route::push($route);

        return $this;
    }

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
    public function put(string $route, $handler): RouteInterface
    {
        return $this->addRoute('PUT', $route, $handler);
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
    public function delete(string $route, $handler): RouteInterface
    {
        return $this->addRoute('DELETE', $route, $handler);
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
        $this->middlewares[] = $middleware;
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
    public function where($parameter, ?string $regExp = null): RouteInterface
    {
        if (is_array($parameter)) {
            $this->parameterTypes['regExp'] = array_merge($this->parameterTypes['regExp'], $parameter);
        } else {
            if (null === $regExp) {
                throw new ValueError('Second parameter must not be null when string is passed to first parameter.');
            }
            $this->parameterTypes['regExp'] += [$parameter => $regExp];
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereNumber(string $parameterName): RouteInterface
    {
        array_push($this->parameterTypes['number'], $parameterName);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereAlpha(string $parameterName): RouteInterface
    {
        array_push($this->parameterTypes['alpha'], $parameterName);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function whereAlphaNumeric(string $parameterName): RouteInterface
    {
        array_push($this->parameterTypes['alphanumeric'], $parameterName);
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
            'middleware' => $this->middlewares,
            'method' => $this->method,
            'name' => $this->name,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'group' => $this->group ?? null,
            'fields' => $this->fields,
            'parameterTypes' => $this->parameterTypes,
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
