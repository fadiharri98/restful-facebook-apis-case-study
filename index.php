<?php
header('Content-Type: application/json; charset=utf-8');

require_once "controllers/BaseController.php";
require_once "constants/StatusCodes.php";
require_once "components/Route.php";

require_once "routes/v1/urls.php";

echo Route::handleRequest();