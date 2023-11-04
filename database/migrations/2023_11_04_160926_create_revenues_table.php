<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
			$table->dateTime('revenue_date');
			$table->string('reference', 255);
			$table->longText('description')->nullable();
			$table->money('amount');
			$table->double('quantity', 10, 2);
			$table->double('vat', 10, 2)->default(0);
			$table->money('sub_total');
			$table->money('vat_total');
			$table->money('total');
			$table->lockable();
            $table->sensesHiddenAt();
            $table->sensesTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('revenues');
    }
};

//Generated 04-11-2023 16:09:26
