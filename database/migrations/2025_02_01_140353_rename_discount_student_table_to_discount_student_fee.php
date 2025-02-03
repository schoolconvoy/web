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
        Schema::rename('discount_student', 'discount_student_fee');

        Schema::table('discount_student_fee', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('discount_student_fee', 'discount_student');
        Schema::table('discount_student_fee', function (Blueprint $table) {
            $table->dropColumn('fee_id');
        });
    }
};
