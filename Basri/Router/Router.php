<?php

namespace Basri\Router;

use Closure;
use Exception;

class Router
{
    private $route;
    private $prefix = '/';
    private $routes = [];

    public function __construct()
    {
        $this->route = $_SERVER['REQUEST_URI'];
    }

    // Route Grouping
    public function group($name, Closure $routes)
    {
        $this->prefix = rtrim($name, '/') . '/';
        $routes($this); // Call the closure with $this
        $this->prefix = '/'; // Reset the prefix after the group is processed
    }

    // Process URL Parameters
    private function runServer($route, $serverRoute, $closure)
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route, $parameterNames);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);

        if (preg_match("#^$pattern$#", $serverRoute, $matches)) {
            array_shift($matches); // Remove full match
            $parameters = array_combine($parameterNames[1], $matches);

            // Handle closure execution
            return $this->handleClosure($closure, $parameters);
        }

        return null;
    }

    // Handle different closure types
    private function handleClosure($closure, $parameters)
    {
        if (is_array($closure)) {
            return call_user_func([new $closure[0], $closure[1]], ...$parameters);
        }

        if (is_string($closure)) {
            [$controller, $method] = explode('@', $closure);
            return call_user_func([new $controller, $method], ...$parameters);
        }

        if (is_callable($closure)) {
            return $closure(...$parameters);
        }

        return null;
    }

    // Define GET route
    public function get(string $url, Closure|string|array $closure, string $name = null)
    {
        return $this->addRoute('GET', $url, $closure, $name);
    }

    // Define POST route
    public function post(string $url, Closure|string|array $closure, string $name = null)
    {
        return $this->addRoute('POST', $url, $closure, $name);
    }

    // Define PUT route
    public function put(string $url, Closure|string|array $closure, string $name = null)
    {
        return $this->addRoute('PUT', $url, $closure, $name);
    }

    // Define DELETE route
    public function delete(string $url, Closure|string|array $closure, string $name = null)
    {
        return $this->addRoute('DELETE', $url, $closure, $name);
    }

    // Helper method to add a route
    private function addRoute($method, $url, $closure, $name)
    {
        $this->routes[$method][] = [
            'path'    => $this->prefix . $url,
            'closure' => $closure,
            'name'    => $name
        ];
        return $this;
    }

    // Dispatch the route
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $route = trim($this->route, '/');

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $path) {
                $uri = trim($path['path'], '/');
                $output = $this->runServer($uri, $route, $path['closure']);

                if ($output !== null) {
                    $this->sendResponse($output);
                    return;
                }
            }
        }

        throw new Exception("404 Not Found: Route does not exist.");
    }

    // Send the appropriate response based on the output type
    private function sendResponse($output)
    {
        $outputType = gettype($output);

        if ($outputType === 'object' || $outputType === 'array') {
            header('Content-Type: application/json');
            echo json_encode($output);
        } else {
            echo $output;
        }
    }
}
