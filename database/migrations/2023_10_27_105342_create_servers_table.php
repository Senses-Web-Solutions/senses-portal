<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
			$table->string('title', 255);
			$table->string('slug');
			$table->string('ip');
			$table->string('os');
			$table->integer('priority');
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servers');
    }
};

//Generated 27-10-2023 10:53:42
