<?php

namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;

class AuthenticationException extends Exception
{
    public function __construct($message="")
    {
        parent::__construct(
            $message ?: "not authorized.",
            StatusCodes::UNAUTHORIZED);
    }
}