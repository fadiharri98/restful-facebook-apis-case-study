<?php
$api_v1 = "api/v1";

Route::GET("$api_v1/users", UserController::class);
Route::POST("$api_v1/users", UserController::class);

Route::GET("$api_v1/users/%s/posts", PostController::class);
Route::POST("$api_v1/users/%s/posts", PostController::class);
