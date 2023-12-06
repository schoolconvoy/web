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
        Schema::table('level_subject', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->change();
            $table->unsignedBigInteger('level_id')->change();

            $table->foreign('subject_id')
                    ->references('id')
                    ->on('subjects');

            $table->foreign('level_id')
                    ->references('id')
                    ->on('levels');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('level_subject', function (Blueprint $table) {
            //
        });
    }
};
