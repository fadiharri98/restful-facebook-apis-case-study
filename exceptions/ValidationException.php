<?php
namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct($message, $code = StatusCodes::VALIDATION_ERROR, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}