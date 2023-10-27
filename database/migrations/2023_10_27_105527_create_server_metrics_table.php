<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('server_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id')->index();
			$table->unsignedBigInteger('company_id')->index();
			$table->float('timestamp');
			$table->float('uptime');
			$table->datetime('logged_at');
			$table->integer('cpu_cores')->nullable();
			$table->integer('cpu_threads')->nullable();
			$table->float('cpu_use')->nullable();
			$table->float('cpu_idle')->nullable();
			$table->float('load_1')->nullable();
			$table->float('load_5')->nullable();
			$table->float('load_15')->nullable();
			$table->integer('ram_free')->nullable();
			$table->integer('ram_used')->nullable();
			$table->integer('disk_free')->nullable();
			$table->integer('disk_used')->nullable();
			$table->integer('swap_free')->nullable();
			$table->integer('swap_used')->nullable();
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('server_metrics');
    }
};

//Generated 27-10-2023 10:55:27
