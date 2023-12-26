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
        Schema::table('classes_fee', function (Blueprint $table) {
            $table->renameColumn('class_id', 'classes_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes_id', function (Blueprint $table) {
            $table->renameColumn('classes_id', 'class_id');
        });
    }
};
