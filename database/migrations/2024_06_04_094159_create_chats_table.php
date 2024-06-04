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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('invited_user_id')->index()->nullable();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('status_id')->index();
            $table->string('name')->default('Visitor');
            $table->string('system');
            $table->jsonb('meta')->nullable();
            $table->dateTime('completed_at')->nullable();

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
        Schema::dropIfExists('chats');
    }
};
