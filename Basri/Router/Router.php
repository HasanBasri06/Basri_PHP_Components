<?php 

namespace Basri\Router;

use Closure;
use Exception;

class Router {
    private string $route;
    private string $prefix = '/';
    private array $routes = [];

    public function __construct() {
        
        $this->route = $_SERVER['REQUEST_URI'];
    }

    public function group($name, $routes) {
        $name = $name . "/";
        $this->prefix = $name;
        return $routes($this);
    }

    private function runServer($requestRoute, $serverRoute, $closure) {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $requestRoute, $parameterNames);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $requestRoute);

        if (preg_match("#^$pattern$#", $serverRoute, $matches)) {
            array_shift($matches);

            $parameters = array_combine($parameterNames[1], $matches);

            if (is_array($closure)) {
                return call_user_func([new $closure[0], $closure[1]], ...$parameters);
            }

            if (is_string($closure)) {
                $expl = explode('@', $closure);
                return call_user_func([new $expl[0], $expl[1]], ...$parameters);
            }

            if (is_object($closure)) {
                return $closure(...$parameters);
            }

        }

        return null;
    }

    public function get(string $url, Closure|string|array $closure, string $name = null) {
        $this->routes['GET'][] = ['path'  => $this->prefix . $url, 'closure' => $closure, 'name' => $name];
        return $this;
    }

    public function post(string $url, Closure|string|array $closure, string $name = null) {
        $this->routes['POST'][] = ['path'  => $this->prefix . $url, 'closure' => $closure, 'name' => $name];
        return $this;
    }

    public function put(string $url, Closure|string|array $closure, string $name = null) {
        $this->routes['PUT'][] = ['path'  => $this->prefix . $url, 'closure' => $closure, 'name' => $name];
        return $this;
    }

    public function delete(string $url, Closure|string|array $closure, string $name = null) {
        $this->routes['DELETE'][] = ['path'  => $this->prefix . $url, 'closure' => $closure, 'name' => $name];
        return $this;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $route = trim($this->route, '/');

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $path) {
                $uri = trim($path['path'], '/');
                $output = $this->runServer($uri, $route, $path['closure']);
            
                if ($output !== null) {
                    $outputType = gettype($output);

                    if ($outputType === 'object' || $outputType == 'array') {
                        header('Content-Type: application/json');
                        echo json_encode($output);
                        return;
                    }

                    echo $output;

                    return;
                }
            }
        }

        throw new Exception("404 Not Found: Route does not exist.");
    }
}
