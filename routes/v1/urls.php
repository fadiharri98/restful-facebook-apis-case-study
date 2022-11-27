<?php
/**
 * Script to register all app URLs.
 * Register URLs by using `Route` component.
 * All URLs in this script are in version 1, so should have suffix "api/v1".
 */
use Components\Route;

use Controllers\UserController;
use Nested\Controllers\UserPostController;
use Controllers\PostController;

$api_v1 = "api/v1";

Route::GET("$api_v1/users", UserController::class);
Route::GET("$api_v1/users/{user_id}", UserController::class, "show");
Route::POST("$api_v1/users", UserController::class);
Route::PUT("$api_v1/users/{user_id}", UserController::class);
Route::DELETE("$api_v1/users/{user_id}", UserController::class);

Route::GET("$api_v1/posts/{post_id}", PostController::class, "show");

Route::GET("$api_v1/users/{user_id}/posts", UserPostController::class);

