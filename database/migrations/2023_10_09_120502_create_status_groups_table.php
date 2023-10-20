<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('status_groups', function (Blueprint $table) {
            $table->id();
            $table->string('title');
			$table->string('slug', 255);
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('status_groups');
    }
};

//Generated 09-10-2023 12:05:02
