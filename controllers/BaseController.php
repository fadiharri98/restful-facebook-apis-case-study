<?php
namespace Controllers;

use Constants\RequestVerbs;
use Exception;

abstract class BaseController
{
    /**
     * Each request verb (method) has an equivalent controller method.
     * Should subclass controller implements them according what did registered in route.
     * Should all these methods be in-accessible by define them as protected or private.
     * @var array|string[]
     */
    private array $handlerMap = [
        RequestVerbs::GET => 'index',
        RequestVerbs::POST => 'create',
        RequestVerbs::PUT => 'update',
        RequestVerbs::DELETE => 'destroy'
    ];

    /**
     * @throws Exception if handler (method) doesn't exist.
     */
    public function __call($name, $arguments)
    {
        $handler = $this->handlerMap[$name] ?? $name;

        if (! method_exists($this, $handler))
        {
            $exception_message = sprintf(
                "Handler `%s` not defined in `%s` controller.",
                $handler,
                get_class($this)
            );
            throw new Exception("$exception_message");
        }

        $response = $this->$handler(...$arguments);

        /**
         * validation: response should always has `data` & `status_code`
         */
        if (! array_key_exists('data', $response) || ! array_key_exists('status_code', $response))
        {
            throw new Exception(
                "data in response should be in `data` key & status code in `status_code` key.");
        }

        return $response;
    }
}