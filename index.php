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
 * register all app APIs (according to RESTFUL-API standards)
 */
require_once "routes/v1/urls.php";

/*
 * handle coming request via Route component
 */
use Components\Route;
echo Route::handleRequest();
