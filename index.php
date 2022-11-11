<?php
require 'vendor/autoload.php';

/*
 * use Dotenv &
 * load environment variables.
 * use `$_ENV` to access variables.
 * (safeLoad) to skip exceptions if `.env` not exist
 */
use Dotenv\Dotenv;
Dotenv::createImmutable(__DIR__)->safeLoad();

/*
 * define response to be always in JSON format (RESTFUL-API)
 */
header('Content-Type: application/json; charset=utf-8');

require_once "constants/StatusCodes.php";
require_once "components/Route.php";

require_once "routes/v1/urls.php";

echo Route::handleRequest();
