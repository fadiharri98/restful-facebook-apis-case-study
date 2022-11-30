<?php

namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;

class AuthorizationException extends Exception
{
    public function __construct($message="")
    {
        parent::__construct(
            $message ?: "not allowed.",
            StatusCodes::FORBIDDEN);
    }
}