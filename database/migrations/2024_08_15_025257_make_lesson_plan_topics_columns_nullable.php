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
        Schema::table('lesson_plan_topics', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_plan_id')->nullable()->change();
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_plan_topics', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_plan_id');
            $table->unsignedBigInteger('subject_id');
        });
    }
};
