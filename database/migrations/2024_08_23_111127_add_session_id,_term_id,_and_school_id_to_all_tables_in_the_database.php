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
        // Add session_id, term_id and school_id to all tables in the database
        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'session_id')) {
                    $table->unsignedBigInteger('session_id')->nullable();
                }
                if (!Schema::hasColumn($table->getTable(), 'term_id')) {
                    $table->unsignedBigInteger('term_id')->nullable();
                }
                if (!Schema::hasColumn($table->getTable(), 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('the_database', function (Blueprint $table) {
            //
        });
    }
};
