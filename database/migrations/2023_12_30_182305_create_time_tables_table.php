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
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('day_of_week');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('term_id');
            $table->timestamps();
        });

        Schema::create('subject_timetable', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamp('start_time')->default(now())->nullable();
            $table->timestamp('end_time')->default(now())->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
