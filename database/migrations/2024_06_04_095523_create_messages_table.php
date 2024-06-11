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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id')->index();
            $table->boolean('from_agent')->default(false);
            $table->string('author')->index();
            $table->mediumText('content');
            $table->jsonb('meta')->nullable();
            $table->datetime('sent_at');
            $table->datetime('read_at')->nullable();
            $table->unsignedBigInteger('status_id')->index();

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
        Schema::dropIfExists('messages');
    }
};
