<?php
/**
 * Script to register all app URLs.
 * Register URLs by using `Route` component.
 * All URLs in this script are in version 1, so should have suffix "api/v1".
 */
use Components\Route;

use Controllers\FooController;
use Nested\Controllers\FooBarController;

$api_v1 = "api/v1";

Route::GET("$api_v1/foo/{user_id}", FooController::class, "show");
Route::POST("$api_v1/foo", FooController::class);
Route::PUT("$api_v1/foo/{user_id}", FooController::class);
Route::DELETE("$api_v1/foo/{user_id}", FooController::class);

Route::GET("$api_v1/foo/{user_id}/bar/{bar_id}", FooBarController::class, "show");
Route::POST("$api_v1/foo/{user_id}/bar", FooBarController::class);
