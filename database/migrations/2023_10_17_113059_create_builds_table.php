<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildsTable extends Migration
{
    public function up()
    {
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->longText('processed_content')->nullable();
            $table->string('slot')->default('default');
            $table->morphs('buildable');
            $table->jsonb('related_models');
            $table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('builds');
    }
}
