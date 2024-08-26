<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeDataBelongToSchool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-data-ownership {school_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schoolId = $this->argument('school_id');

        // Get all the table names in the database
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        $totalRowsUpdated = 0;

        foreach ($tables as $table) {
            // Check if the table has the 'school_id' column
            if (!Str::startsWith($table, 'pulse_') && !Str::startsWith($table, 'migrations') && !Str::startsWith($table, 'telescope')) {
                // Check if the table has the 'school_id' column
                if (Schema::hasColumn($table, 'school_id')) {
                    // Update the 'school_id' column with the provided value
                    $rowsUpdated = DB::table($table)->whereNull('school_id')->update(['school_id' => $schoolId]);
                    $totalRowsUpdated += $rowsUpdated;
                }
            }
        }

        $this->info('Data ownership updated successfully.');
        $endTime = microtime(true);
        $executionTime = $endTime - LARAVEL_START;
        $this->info($totalRowsUpdated . ' rows updated in ' . round($executionTime, 2) . ' seconds.');
    }
}
