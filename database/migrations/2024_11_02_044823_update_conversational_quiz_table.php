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
        Schema::table('conversation_quiz', function ($table) {
            // Delete stale columns
            $table->dropColumn('correct_option_index');
            $table->dropColumn('score');

            // Poll ID for the current question
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('attempt_id');
        });

        Schema::rename('conversation_quiz', 'quiz_session');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('quiz_session', 'conversation_quiz');
    }
};
