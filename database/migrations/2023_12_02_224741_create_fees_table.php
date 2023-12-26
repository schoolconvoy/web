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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->unsignedBigInteger('fee_category');
            $table->string('description')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });

        Schema::create('fee_student', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_id');
            $table->unsignedBigInteger('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
