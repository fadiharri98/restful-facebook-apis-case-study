<?php
namespace CustomExceptions;

use Constants\StatusCodes;
use Exception;

class ResourceNotFoundException extends Exception
{
    public function __construct($resource) {

        parent::__construct("$resource not found.", StatusCodes::NOT_FOUND, null);
    }
}