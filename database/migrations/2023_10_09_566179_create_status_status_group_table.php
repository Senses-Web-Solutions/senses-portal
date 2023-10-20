<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('status_status_group', function (Blueprint $table) {
            $table->id();
            $table->integer('status_id')->unsigned()->index();
            $table->integer('status_group_id')->unsigned()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('status_status_group');
    }
};

//Generated 09-10-2023 12:05:02
