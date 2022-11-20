<?php
namespace Controllers;

use Components\ValidationComponent;
use Constants\RequestVerbs;
use Exception;
use Helpers\RequestHelper;

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

    protected ValidationComponent $validationComponent;

    /**
     * register rules for each handler in 3 levels -> url, query, and payload
     * supported rules are listed in constants.Rules
     * schema structure
     * [
     *  ...,
     *  'handler' => [
     *      'url' => [
     *          ...
     *      ],
     *      'query' => [
     *          ...
     *      ],
     *      'payload' => [
     *          ...
     *      ]
     *  ],
     *  ...
     * ]
     * @var array $validationSchema
     */
    protected array $validationSchema;

    public function __construct()
    {
        $this->validationComponent = new ValidationComponent();
    }

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

        $handler_validation = ($this->validationSchema[$handler] ?? []);

        if (key_exists('url', $handler_validation))
        {
            $this->validationComponent->validateUrlParams($handler_validation['url'], [...$arguments]);
        }

        if (key_exists('query', $handler_validation))
        {
            $queryParams = RequestHelper::getQueryParams();
            $this->validationComponent->validateQueryParams($handler_validation['query'], $queryParams);
        }

        if (key_exists('payload', $handler_validation))
        {
            $payload = RequestHelper::getRequestPayload();
            $this->validationComponent->validateRequestPayload($handler_validation['payload'], $payload);
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