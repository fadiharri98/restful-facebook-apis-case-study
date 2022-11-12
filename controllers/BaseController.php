<?php

abstract class BaseController
{
    protected abstract function get();
    protected abstract function post();

    public function __call($name, $arguments)
    {
        $response = $this->$name(...$arguments);

        // validate response
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