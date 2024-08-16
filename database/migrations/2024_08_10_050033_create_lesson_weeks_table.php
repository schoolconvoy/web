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
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('session');
            $table->unsignedBigInteger('term');
            $table->integer('order');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();


            $table->foreign('session')->references('id')->on('sessions');
            $table->foreign('term')->references('id')->on('terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weeks');
    }
};
