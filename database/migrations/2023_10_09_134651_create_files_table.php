<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
			$table->string('stored_name', 255);
			$table->longText('path');
			$table->string('folder', 255)->nullable();
			$table->string('mime_type', 255);
			$table->string('extension', 255);
			$table->integer('size');
			$table->string('disk', 255)->nullable();
			$table->string('preview_path', 255)->nullable();
			$table->string('preview_disk', 255)->nullable();
			$table->string('print_path', 255)->nullable();
			$table->string('print_disk', 255)->nullable();
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};

//Generated 09-10-2023 13:46:51
