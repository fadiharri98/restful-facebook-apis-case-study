<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create('users', function (Blueprint $table) {
    $table->id();

    $table->string('name', 250);
    $table->string('email', 250)->unique();
    $table->string('username', 250)->unique();

    $table->string('password', 500);
    $table->string('profile_img', 1000)->nullable();

    $table->timestamp('created')->useCurrent();
});