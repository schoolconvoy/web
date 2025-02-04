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
        // Schema::table('payment_reminders', function (Blueprint $table) {
        //     $table->dropConstrainedForeignId('fee_id');
        // });

        // Schema::create('payment_reminder_fees', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('fee_id')->constrained();
        //     $table->foreignId('payment_reminder_id')->constrained();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
