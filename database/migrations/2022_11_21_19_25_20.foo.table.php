<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;

Manager::schema()->create('foos', function (Blueprint $table) {
    $table->id();
    $table->string('dummy', 250);
    $table->timestamp('created')->useCurrent();
});