<?php

namespace helpers;

use Exception;

/**
 *
 */
class Router {
    /**
     * @var array
     */
    private array $routes;

    /**
     * @var mixed|string
     */
    private mixed $notFoundController;

    private string $requestPath;

    private string $requestMethod;

    private string $basePath;

    /**
     *
     */
    public function __construct() {
        $this->routes = [];

        $this->notFoundController = '';

        $this->requestPath = parse_url($_SERVER['REQUEST_URI'])['path'];

        if ($this->requestPath !== '/') {
            $this->requestPath = rtrim($this->requestPath, '/');
        }

        $this->requestMethod = $_SERVER['REQUEST_METHOD'];

        $this->basePath = '';
    }

    /**
     * @param string     $method
     * @param string     $pattern
     * @param callable   $controller
     * @param array|null $before
     * @param array|null $after
     *
     * @return void
     */
    private function add(string $method, string $pattern, callable $controller, ?array $before = null, ?array $after = null): void {
        if (!isset($this->routes[$this->basePath . $pattern])) {
            $this->routes[$this->basePath . $pattern] = [];
        }

        $this->routes[$this->basePath . $pattern][$method] = [
            'method' => $method,
            'pattern' => $this->basePath . $pattern,
            'controller' => $controller,
            'before' => $before,
            'after' => $after
        ];
    }

    /**
     * @param string   $type
     * @param string   $method
     * @param string   $pattern
     * @param callable $controller
     *
     * @return void
     */
    private function addFilter(string $type, string $method, string $pattern, callable $controller): void {
        if (!isset($this->routes[$this->basePath . $pattern])) return;

        if (!isset($this->routes[$this->basePath . $pattern][$method])) return;

        if ($type === 'before') {
            $this->routes[$this->basePath . $pattern][$method]['before'][] = $controller;
        }

        if ($type === 'after') {
            $this->routes[$this->basePath . $pattern][$method]['after'][] = $controller;
        }
    }

    /**
     * @param string $basePath
     *
     * @return void
     */
    public function setBasePath(string $basePath): void {
        $this->basePath = $basePath;
    }

    /**
     * @param string     $pattern
     * @param callable   $controller
     * @param array|null $before
     * @param array|null $after
     *
     * @return void
     * @see self::add()
     */
    public function get(string $pattern, callable $controller, ?array $before = null, ?array $after = null): void {
        $this->add('GET', $pattern, $controller, $before, $after);
    }

    /**
     * @param string     $pattern
     * @param callable   $controller
     * @param array|null $before
     * @param array|null $after
     *
     * @return void
     * @see self::add()
     */
    public function post(string $pattern, callable $controller, ?array $before = null, ?array $after = null): void {
        $this->add('POST', $pattern, $controller, $before, $after);
    }

    /**
     * @param string     $pattern
     * @param callable   $controller
     * @param array|null $before
     * @param array|null $after
     *
     * @return void
     * @see self::add()
     */
    public function put(string $pattern, callable $controller, ?array $before = null, ?array $after = null): void {
        $this->add('PUT', $pattern, $controller, $before, $after);
    }

    /**
     * @param string     $pattern
     * @param callable   $controller
     * @param array|null $before
     * @param array|null $after
     *
     * @return void
     * @see self::add()
     */
    public function delete(string $pattern, callable $controller, ?array $before = null, ?array $after = null): void {
        $this->add('DELETE', $pattern, $controller, $before, $after);
    }

    /**
     * @param string   $method
     * @param string   $pattern
     * @param callable $controller
     *
     * @return void
     */
    public function before(string $method, string $pattern, callable $controller): void {
        $this->addFilter('before', $method, $pattern, $controller);
    }

    /**
     * @param string   $method
     * @param string   $pattern
     * @param callable $controller
     *
     * @return void
     * @throws Exception
     */
    public function after(string $method, string $pattern, callable $controller): void {
        $this->addFilter('after', $method, $pattern, $controller);
    }

    /**
     * @param callable $controller
     *
     * @return void
     */
    public function set404NotFound(callable $controller): void {
        $this->notFoundController = $controller;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run(): void {
        $matches = [];

        $foundRoute = null;

        foreach ($this->routes as $pattern => $methods) {
            if (!preg_match('#^' . $pattern . '$#', $this->requestPath, $matches))
                continue;

            if (isset($methods[$this->requestMethod]))
                $foundRoute = $methods[$this->requestMethod];

            break;
        }

        if (!$foundRoute) {
            if (is_callable($this->notFoundController)) {
                call_user_func($this->notFoundController);
                return;
            } else throw new Exception('The route does not exist', 404);
        }

        $matches = array_slice($matches, 1);

        if (!is_callable($foundRoute['controller'])) {
            throw new Exception('Either the class or method does not exist', 404);
        }

        //Call before handlers
        if (isset($foundRoute['before']) && is_array($foundRoute['before'])) {
            foreach ($foundRoute['before'] as $before) {
                if (is_callable($before)) {
                    if (!call_user_func_array($before, $matches)) return;
                }
            }
        }

        //Call controller
        call_user_func_array($foundRoute['controller'], $matches);

        //Call after handlers
        if (isset($foundRoute['after']) && is_array($foundRoute['after'])) {
            foreach ($foundRoute['after'] as $after) {
                if (is_callable($after)) {
                    if (!call_user_func_array($after, $matches)) return;
                }
            }
        }
    }
}