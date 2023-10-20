<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tag_tag_group', function (Blueprint $table) {
            $table->id();
            $table->integer('tag_id')->unsigned()->index();
            $table->integer('tag_group_id')->unsigned()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tag_tag_group');
    }
};

//Generated 09-10-2023 10:27:05
