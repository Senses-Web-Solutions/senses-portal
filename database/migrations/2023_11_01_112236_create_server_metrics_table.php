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
			$table->unsignedBigInteger('timestamp')->nullable();
			$table->unsignedBigInteger('uptime')->nullable();
			$table->float('cpu_use')->nullable();
			$table->float('cpu_us')->nullable();
			$table->float('cpu_sy')->nullable();
			$table->float('cpu_ni')->nullable();
			$table->float('cpu_id')->nullable();
			$table->float('cpu_wa')->nullable();
			$table->float('cpu_hi')->nullable();
			$table->float('cpu_si')->nullable();
			$table->float('cpu_st')->nullable();
			$table->float('load_1')->nullable();
			$table->float('load_5')->nullable();
			$table->float('load_15')->nullable();
			$table->unsignedInteger('ram_total')->nullable();
			$table->unsignedInteger('ram_free')->nullable();
			$table->unsignedInteger('ram_buffer')->nullable();
			$table->unsignedInteger('ram_cache')->nullable();
			$table->unsignedInteger('ram_used')->nullable();
			$table->unsignedInteger('swap_total')->nullable();
			$table->unsignedInteger('swap_free')->nullable();
			$table->unsignedInteger('swap_used')->nullable();
			$table->unsignedInteger('swap_cache')->nullable();
			$table->unsignedBigInteger('disk_total')->nullable();
			$table->unsignedBigInteger('disk_free')->nullable();
			$table->unsignedBigInteger('disk_used')->nullable();
			$table->unsignedBigInteger('disk_read')->nullable();
			$table->unsignedBigInteger('disk_write')->nullable();
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

//Generated 01-11-2023 11:22:36
