<?php

namespace DTL\MVC\Controller;
use DTL\MVC\Controller\Request;
use DTL\MVC\Logger;

class Dispatcher
{
    protected $routes = array();

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(Request $request)
    {
        $path = $request->getPathInfo();

        foreach ($this->routes as $route) {
            if (preg_match($route->pattern, $path, $matches)) {
                array_shift($matches); // drop first preg_match element
                $params = array_combine($route->params, $matches);
                Logger::controller()->info('Matched route "'.$route->pattern.'" to "'.$route->target.'"', $params);
            }
        }
    }
}
