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
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('week_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('lesson_plan_topic_id');
            $table->tinyInteger('status');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('period');
            $table->string('duration')->nullable();
            $table->text('objectives');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('term_id');
            $table->timestamps();
        });

        Schema::create('lesson_plan_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('lesson_plan_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->timestamps();
        });

        Schema::create('lesson_plan_reviews', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->tinyInteger('status');
            $table->unsignedBigInteger('lesson_plan_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
