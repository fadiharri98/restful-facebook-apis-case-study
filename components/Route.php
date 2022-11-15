<?php
namespace Components;

use Exception;
use Constants\StatusCodes;

class Route
{
    const SUPPORTED_METHODS = ['GET', 'POST'];

    private static $_ROUTES = [];

    private static function register($method, $url, $controller)
    {
        if (! in_array($method, self::SUPPORTED_METHODS)) {
            throw new Exception("Route::$method, `$method` isn't supported.");
        }

        self::$_ROUTES[$url][$method] = $controller;
    }

    /**
     * @param array $url_parts
     * @return array
     */
    private static function mapUrlWithParams(array $url_parts): array
    {
        /*
         * mapping target route (as Url) & detecting its route-params with passed url.
         * if mapping failed, an empty array will be the result.
        array structure like
            [
                'url' => "",
                'params' => []
            ]
        array can be empty if not route is found.
         */
        foreach (array_keys(self::$_ROUTES) as $route)
        {
            $registered_url_parts = explode("/", $route);
            $params = [];
            $break_loop = false;

            if (sizeof($registered_url_parts) != sizeof($url_parts))
            {
                continue;
            }

            foreach ($registered_url_parts as $index => $registered_url_part)
            {
                // handle {.whatever} as param.
                if (preg_match("/^{.*}$/", $registered_url_part))
                {
                    $params[] = $url_parts[$index];
                }
                elseif ($registered_url_part != $url_parts[$index])
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
                    'url' => $route,
                    'params' => $params
                ];
            }
        }

        return [];
    }

    public static function GET($url, $controller)
    {
        self::register('GET', $url, $controller);
    }

    public static function POST($url, $controller)
    {
        self::register('POST', $url, $controller);
    }

    public static function handleRequest()
    {
        $request_url_parts = array_slice(explode('/', $_SERVER['REQUEST_URI']), 2);
        $request_method = $_SERVER['REQUEST_METHOD'];

        $url_params = self::mapUrlWithParams($request_url_parts);

        $url = $url_params['url'] ?? "";
        $params = $url_params['params'] ?? "";

        if (! $url_params || ! key_exists($url, self::$_ROUTES))
        {
            $response = [
                'message' => "not found.",
                'status_code' => StatusCodes::NOT_FOUND
            ];
        }
        elseif(! key_exists($request_method, self::$_ROUTES[$url]))
        {
            $response = [
                'message' => "$request_method request method isn't supported for this url.",
                'status_code' => StatusCodes::METHOD_NOT_ALLOWED
            ];
        }
        else
        {
            $controller = self::$_ROUTES[$url][$request_method];

            $response = (new $controller())->$request_method(... $params);
        }

        http_response_code($response['status_code']);

        return json_encode($response);
    }
}