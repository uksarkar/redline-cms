<?php

namespace RedlineCms\Core\Http;

use FastRoute\RouteCollector;
use RedlineCms\Core\Support\Path;

class Route
{
    private array $routes;
    private static $instance;
    private static $options;

    private function __construct()
    {
        $this->routes = [];
    }

    public function middleware(Middleware|string $middleware): self
    {
        $lastIndex = count($this->routes) - 1;

        if(array_key_exists($lastIndex, $this->routes)) {
            $middlewares = $this->routes[$lastIndex]["middlewares"] ?? [];
            $middlewares[] = $middleware;
            $this->routes[$lastIndex]["middlewares"] = $middlewares;
        }

        return $this;
    }

    private function addRoute(string|array $method, string $path, string $controller, string $action, array $middlewares = null)
    {
        $this->routes[] = [
            'path' => $path,
            'method' => $method,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares
        ];

        return $this;
    }

    private static function getInstance(): self
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    // Dynamically handle any HTTP method
    public static function add(string|array $method, string $path, string $controller, string $action)
    {
        if(isset(static::$options) && is_array(static::$options)) {
            if(isset(static::$options["path"])) {
                $path = rtrim(Path::join(static::$options["path"], $path), "/");
            }

            $middlewares = static::$options["middlewares"] ?? null;
        }

        return static::getInstance()->addRoute($method, $path, $controller, $action, $middlewares ?? null);
    }

    // Shortcut methods for common HTTP methods
    public static function get(string $path, string $controller, string $action)
    {
        return static::add(['GET', 'HEAD'], $path, $controller, $action);
    }

    public static function post(string $path, string $controller, string $action)
    {
        return static::add('POST', $path, $controller, $action);
    }

    public static function put(string $path, string $controller, string $action)
    {
        return static::add('PUT', $path, $controller, $action);
    }

    public static function patch(string $path, string $controller, string $action)
    {
        return static::add('PATCH', $path, $controller, $action);
    }

    public static function delete(string $path, string $controller, string $action)
    {
        return static::add('DELETE', $path, $controller, $action);
    }

    public static function group(array $options, \Closure $cb)
    {
        static::$options = $options;
        $cb();
        static::$options = null;
    }

    public static function init(RouteCollector $r)
    {
        // Register all routes with FastRoute
        foreach (static::getInstance()->routes as $route) {
            $r->addRoute(
                $route['method'], 
                $route['path'], 
                [$route['controller'], $route['action'], $route["middlewares"]]
            );
        }
    }
}
