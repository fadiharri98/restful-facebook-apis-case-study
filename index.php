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

/*
 * register all app URLs (according to RESTFUL-API standards)
 */
require_once "routes/v1/urls.php";

/*
 * handle coming request via Route component
 */
use Components\Route;
try {
    echo Route::handleRequest();
} catch (Exception $e) {
    // check if we in debug mode first, so we can clear what exactly the exception is.
    $debugModeIsActive = (($_ENV['DEBUG_MODE'] ?? "false") == "true");

    $exceptionResponse = [
        "error" => "Internal server error.",
        "status_code" => \Constants\StatusCodes::INTERNAL_ERROR
    ];

    if($debugModeIsActive) {
        $exceptionResponse['error'] = $e->getMessage();
    }

    echo json_encode($exceptionResponse);
}