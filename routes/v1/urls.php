<?php
use Components\Route;
use Controllers\UserController;
use Controllers\PostController;
use Controllers\LikeController;

$api_v1 = "api/v1";

Route::GET("$api_v1/users", UserController::class);
Route::POST("$api_v1/users", UserController::class);

Route::GET("$api_v1/users/{user_id}/posts", PostController::class);
Route::POST("$api_v1/users/{user_id}/posts", PostController::class);

Route::GET("$api_v1/posts/{post_id}/likes", LikeController::class);
Route::POST("$api_v1/posts/{post_id}/likes", LikeController::class);