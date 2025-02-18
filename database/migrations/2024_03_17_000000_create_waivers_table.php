<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waivers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('waiver_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waiver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiver_fees');
        Schema::dropIfExists('waivers');
    }
};
