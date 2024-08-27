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
        Schema::table('payment_reminders', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->constrained();
            $table->foreignId('term_id')->nullable()->constrained();
            $table->foreignId('school_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_reminders', function (Blueprint $table) {
            //
        });
    }
};
