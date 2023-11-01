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
			$table->string('hostname', 255);
			$table->string('ip_address');
			$table->string('os');
			$table->string('architecture')->nullable();
			$table->integer('cpu_cores')->nullable();
			$table->integer('cpu_threads')->nullable();
			$table->string('distro')->nullable();
			$table->string('distro_version')->nullable();
			$table->string('kernel')->nullable();
			$table->string('kernel_version')->nullable();
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

//Generated 01-11-2023 11:27:41
