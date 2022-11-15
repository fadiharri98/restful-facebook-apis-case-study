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

        self::$_ROUTES[$uri][$method] = $controller;
    }

    /**
     * @param array $uri_parts
     * @return array
     */
    private static function mapUriWithParams(array $uri_parts): array
    {
        /*
         * mapping target route (as Uri) & detecting its route-params with passed uri.
         * if mapping failed, an empty array will be the result.
        array structure like
            [
                'uri' => "",
                'params' => []
            ]
        array can be empty if not route is found.
         */
        foreach (array_keys(self::$_ROUTES) as $route)
        {
            $registered_uri_parts = explode("/", $route);
            $params = [];
            $break_loop = false;

            if (sizeof($registered_uri_parts) != sizeof($uri_parts))
            {
                continue;
            }

            foreach ($registered_uri_parts as $index => $registered_uri_part)
            {
                // handle {.whatever} as param.
                if (preg_match("/^{.*}$/", $registered_uri_part))
                {
                    $params[] = $uri_parts[$index];
                }
                elseif ($registered_uri_part != $uri_parts[$index])
                {
                    // reset params
                    $params = [];
                    $break_loop = true;
                    break;
                }
            }

            if (! $break_loop)
            {
                return [
                    'uri' => $route,
                    'params' => $params
                ];
            }
        }

        return [];
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
        $request_uri_parts = array_slice(explode('/', $_SERVER['REQUEST_URI']), 2);
        $request_method = $_SERVER['REQUEST_METHOD'];

        $uri_params = self::mapUriWithParams($request_uri_parts);

        $uri = $uri_params['uri'] ?? "";
        $params = $uri_params['params'] ?? "";

        if (! $uri_params || ! key_exists($uri, self::$_ROUTES))
        {
            $response = [
                'message' => "not found.",
                'status_code' => StatusCodes::NOT_FOUND
            ];
        }
        elseif(! key_exists($request_method, self::$_ROUTES[$uri]))
        {
            $response = [
                'message' => "$request_method request method isn't supported for this uri.",
                'status_code' => StatusCodes::METHOD_NOT_ALLOWED
            ];
        }
        else
        {
            $controller = self::$_ROUTES[$uri][$request_method];
            require_once "controllers/$controller.php";

            $response = (new $controller())->$request_method(... $params);
        }

        http_response_code($response['status_code']);

        return json_encode($response);
    }
}