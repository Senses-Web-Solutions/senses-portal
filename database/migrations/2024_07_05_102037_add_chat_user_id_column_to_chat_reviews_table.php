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
        Schema::table('chat_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('chat_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_reviews', function (Blueprint $table) {
            $table->dropColumn('chat_user_id');
        });
    }
};
