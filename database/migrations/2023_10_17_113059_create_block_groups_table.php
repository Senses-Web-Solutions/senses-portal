<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('block_groups', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
			$table->string('name');
			$table->unsignedBigInteger('builder_category_id')->index()->nullable();
			$table->string('description')->nullable();
			$table->jsonb('block_group_types')->nullable();
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('block_groups');
    }
}
