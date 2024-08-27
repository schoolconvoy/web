<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MakeDataBelongToSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-data-session {session_id} {school_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Given a session ID, we will set the session ID and also pick the currently active term for the session.
    If no term is active, we will pick the first term for the session.';

    /**
     * Execute the console command.
     *
     * Given a session ID, we will set the session ID and also pick the currently active term for the session.
     * If no term is active, we will pick the first term for the session.
     */
    public function handle()
    {
        $sessionId = $this->argument('session_id');
        $school_id = $this->argument('school_id');

        // Get all the table names in the database
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        $totalRowsUpdated = 0;

        foreach ($tables as $table) {
            // Check if the table has the 'session_id' column
            if (!Str::startsWith($table, 'pulse_') && !Str::startsWith($table, 'migrations') && !Str::startsWith($table, 'telescope')) {
                // Check if the table has the 'session_id' column
                if (Schema::hasColumn($table, 'session_id') && Schema::hasColumn($table, 'term_id')) {
                    $session = Session::where('school_id', $school_id)
                                        ->where('id', $sessionId)
                                        ->first();

                    $term = $session->terms()->where('active', true)->first();
                    if (!$term) {
                        $term = $session->terms()->first();
                    }

                    // Update the 'session_id' column with the provided value
                    $rowsUpdated = DB::table($table)
                                        // ->whereNull('session_id')
                                        ->update([
                                            'session_id' => $sessionId,
                                            'term_id' => $term->id
                                        ]);
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
