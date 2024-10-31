<?php 

namespace Basri\Router;

use Closure;

class Router {
    private string $route;
    private string $prefix = '/';

    public function __construct() {
        $this->route = $_SERVER['REQUEST_URI'];
    }

    public function group($name, $routes) {
        $name = $name."/";
        $this->prefix = $name;
        return $routes($this);
    }

    public function get(string $url, Closure|string|array $closure) {
        $route = trim($this->route, '/');
        $uri = trim($this->prefix . $url, '/');

        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $uri, $parameterNames);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $uri);

        if (preg_match("#^$pattern$#", $route, $matches)) {
            array_shift($matches);

            $parameters = array_combine($parameterNames[1], $matches);

            if (is_array($closure)) {
                echo call_user_func([new $closure[0], $closure[1]], ...$parameters);
            }

            if (is_string($closure)) {
                $expl = explode('@', $closure);
                echo call_user_func([new $expl[0], $expl[1]], ...$parameters);
            }

            if (is_object($closure)) {
                echo $closure(...$parameters);
            }
        }
    }
}
