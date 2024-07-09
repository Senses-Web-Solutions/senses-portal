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
        Schema::table('chats', function (Blueprint $table) {
            $table->string('language', 50)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('device_resolution', 50)->nullable();
            $table->string('tab_resolution', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('browser_version', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('os_version', 50)->nullable();
            $table->string('device', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('language');
            $table->dropColumn('timezone');
            $table->dropColumn('device_resolution');
            $table->dropColumn('tab_resolution');
            $table->dropColumn('browser');
            $table->dropColumn('browser_version');
            $table->dropColumn('os');
            $table->dropColumn('os_version');
            $table->dropColumn('device');
        });
    }
};
