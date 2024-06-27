<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id')->index();
            $table->tinyInteger('knowledge');
            $table->tinyInteger('friendliness');
            $table->tinyInteger('responsiveness');
            $table->float('overall');

            $table->text('comment')->nullable();

            $table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
