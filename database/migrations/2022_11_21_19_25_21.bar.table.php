<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create('bars', function (Blueprint $table) {
    $table->id();
    $table->integer('value');
    $table->foreignId('foo_id')->constrained();
    $table->timestamp('created')->useCurrent();
});