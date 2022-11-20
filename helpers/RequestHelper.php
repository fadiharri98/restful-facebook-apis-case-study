<?php
namespace Helpers;

class RequestHelper
{
    /**
     * ?param=value&... into ['param' => 'value', ...]
     * @return array
     */
    public static function getQueryParams(): array
    {
        return $_GET;
    }

    /**
     * Get raw data sent from client as payload in json format.
     * @return array
     */
    public static function getRequestPayload(): array
    {
        $dataInJsonFormat = file_get_contents('php://input');
        return json_decode($dataInJsonFormat, true);
    }

    /**
     * Standard request uri should have one question mark (?) that split between uri & query params.
     * @param $uri
     * @return string
     */
    public static function getUriWithoutQueryParams($uri): string
    {
        /**
         * 2 parts expected -> uri part, & query params part
         * @var array $request_parts
         */
        $request_parts = explode('?', $uri);

        // get uri part
        return array_shift($request_parts);
    }

    /**
     * Splitting the uri into parts (array) except for the domain.
     * E.g: splitting https://example.com/api/version/resource into ['api', 'version', 'resource'].
     * @param $uri
     * @return array
     */
    public static function splittingUriPath($uri): array
    {
        return array_slice(explode('/', $uri), 2);
    }

}