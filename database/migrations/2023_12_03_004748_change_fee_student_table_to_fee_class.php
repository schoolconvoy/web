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
        Schema::rename('fee_student', 'fee_class');

        Schema::table('fee_class', function (Blueprint $table) {
            $table->renameColumn('student_id', 'class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('fee_class', 'fee_student');

        Schema::table('fee_class', function (Blueprint $table) {
            $table->renameColumn('class_id', 'student_id');
        });
    }
};
