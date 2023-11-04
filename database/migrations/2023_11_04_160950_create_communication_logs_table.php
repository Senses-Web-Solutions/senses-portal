<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
			$table->string('type');
			$table->jsonb('data')->nullable();
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('communication_logs');
    }
};

//Generated 04-11-2023 16:09:50
