<?php
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create('shares', function (Blueprint $table) {
    $table->id();

    $table->foreignId('post_id')->constrained();
    $table->foreignId('user_id')->constrained();

    $table->unique(['user_id', 'post_id']);

    $table->timestamp('created')->useCurrent();
});
