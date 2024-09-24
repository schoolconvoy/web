<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToATable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-soft-delete-to-a-table {table_name}';

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
        $table = $this->argument('table_name');

        Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }
}
