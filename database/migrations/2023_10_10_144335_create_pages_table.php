<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
			$table->string('slug');
			$table->longText('excerpt')->nullable();
			$table->string('type');
			$table->unsignedBigInteger('status_id')->nullable();
			$table->string('meta_title')->nullable();
			$table->longText('meta_description')->nullable();
			$table->boolean('featured')->default();
			$table->boolean('show_last_updated')->default(1);
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};

//Generated 10-10-2023 14:43:35
