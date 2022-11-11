<?php

class Route
{
    const SUPPORTED_METHODS = ['GET', 'POST'];

    private static $_ROUTES = [];

    private static function register($method, $uri, $controller)
    {
        if (! in_array($method, self::SUPPORTED_METHODS)) {
            throw new Exception("Route::$method, `$method` isn't supported.");
        }

        require_once "controllers/$controller.php";
        self::$_ROUTES[$uri][$method] = $controller::$method();
    }

    public static function GET($uri, $controller)
    {
        self::register('GET', $uri, $controller);
    }

    public static function POST($uri, $controller)
    {
        self::register('POST', $uri, $controller);
    }

    public static function handleRequest()
    {
        $path =
            join(
                "/",
                array_slice(explode('/', $_SERVER['REQUEST_URI']), 2)
            );
        $method = $_SERVER['REQUEST_METHOD'];

        if (! key_exists($path, self::$_ROUTES))
        {
            $response = [
                'message' => "not found.",
                'status_code' => StatusCodes::NOT_FOUND
            ];
            http_response_code(StatusCodes::NOT_FOUND);
        }
        elseif(! key_exists($method, self::$_ROUTES[$path]))
        {
            $response = [
                'message' => "method `$method` not supported for this path.",
                'status_code' => StatusCodes::METHOD_NOT_ALLOWED
            ];
            http_response_code(StatusCodes::METHOD_NOT_ALLOWED);
        }
        else
        {
            $response = self::$_ROUTES[$path][$method];
        }

        return json_encode($response);
    }
}