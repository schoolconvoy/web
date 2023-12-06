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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('level_id');
            $table->bigInteger('school_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('classes_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('classes_id');
            $table->string('role'); // teacher or student
            $table->tinyInteger('status');
            $table->bigInteger('term_id');
            $table->bigInteger('session_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
        Schema::dropIfExists('classes_users');
        Schema::table('classes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
