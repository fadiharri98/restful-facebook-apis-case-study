<?php
namespace Controllers;

use Constants\StatusCodes;
use Exception;

abstract class BaseController
{
    /**
     * The default representing the structure of expected response
     * @var array
     */
    private array $defaultResponse = [
        'data' => '',
        'status_code' => StatusCodes::SUCCESS
    ];

    /**
     * Should override by subclass to provide custom behavior
     * @return array
     */
    protected function get(): array
    {
        return $this->defaultResponse;
    }

    /**
     * Should override by subclass to provide custom behavior
     * @return array
     */
    protected function post(): array
    {
        return $this->defaultResponse;
    }

    /**
     * Should override by subclass to provide custom behavior
     * @return array
     */
    protected function put(): array
    {
        return $this->defaultResponse;
    }

    /**
     * Should override by subclass to provide custom behavior
     * @return array
     */
    protected function delete(): array
    {
        return $this->defaultResponse;
    }

    public function __call($name, $arguments)
    {
        $response = $this->$name(...$arguments);

        /**
         * validation: response should always has `data` & `status_code`
         */
        if (! array_key_exists('data', $response))
        {
            throw new Exception("response should contains data associated with `data` key.");
        }
        elseif (! array_key_exists('status_code', $response))
        {
            throw new Exception("response should contains a status code associated with `status_code` key.");
        }

        return $response;
    }
}