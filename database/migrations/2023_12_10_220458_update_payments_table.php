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
        Schema::table('payments', function (Blueprint $table) {
            $table->removeColumn('invoice_id');
            $table->removeColumn('fee_id');
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_id');
            $table->unsignedBigInteger('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('fee_id');
        });
    }
};
