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
        Schema::table('lesson_plan_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewed_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_plan_reviews', function (Blueprint $table) {
            $table->dropColumn('reviewed_by');
        });
    }
};
