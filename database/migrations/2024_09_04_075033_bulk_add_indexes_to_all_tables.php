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
        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (
                    Schema::hasColumn($table->getTable(), 'deleted_at')
                    &&
                    Schema::hasColumn($table->getTable(), 'school_id')
                    &&
                    Schema::hasColumn($table->getTable(), 'class_id')
                    &&
                    Schema::hasColumn($table->getTable(), 'session_id')
                    &&
                    Schema::hasColumn($table->getTable(), 'term_id')
                    &&
                    !Schema::hasIndex($table->getTable(), ['school_id', 'class_id', 'session_id', 'term_id', 'deleted_at'])
                ) {
                    $table->index(['school_id', 'class_id', 'session_id', 'term_id', 'deleted_at'], 'frequently_queried_columns');
                }

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            //
        });
    }
};
