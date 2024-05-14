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
        Schema::table('users', function (Blueprint $table) {
            $table->string('lga')->nullable();
            $table->string('state')->nullable();
            $table->string('year_of_entry')->nullable();
            $table->string('class_at_entry')->nullable();
            $table->string('entrance_score')->nullable();
            $table->string('middle_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('lga');
            $table->dropColumn('state');
            $table->dropColumn('year_of_entry');
            $table->dropColumn('class_at_entry');
            $table->dropColumn('entrance_score');
            $table->dropColumn('middle_name');
        });
    }
};
