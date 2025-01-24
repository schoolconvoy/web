<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExportUsersByRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * This command expects a "role" argument, e.g.: php artisan users:export admin
     */
    protected $signature = 'app:users-export {role : The role to filter by}';

    /**
     * The console command description.
     */
    protected $description = 'Export all users with a given role to a CSV file including their email, name, role, and password (plain text).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the role argument from the command line
        $role = $this->argument('role');

        // Fetch all users matching the specified role
        $users = User::role($role)->get();

        // Define a file path to store the CSV in storage/app (adjust as needed)
        $filePath = storage_path("users_by_role_{$role}.csv");

        // Open file handle for writing
        $handle = fopen($filePath, 'w');

        // Write CSV header
        fputcsv($handle, ['email', 'name', 'role', 'password']);

        // Loop through users and write each row
        foreach ($users as $user) {
            $plain_password = Str::random(8);
            $user->password = Hash::make($plain_password);

            fputcsv($handle, [
                $user->email,
                $user->fullname,
                $role,
                // If "password" is hashed in DB, you'll only see the hash here.
                // If you truly store it in plain text (not recommended), it will appear.
                $plain_password
            ]);
        }

        // Close file handle
        fclose($handle);

        // Inform the user via console
        $this->info("Exported " . $users->count() . " user(s) with role '{$role}' to: {$filePath}");

        return 0; // success exit code
    }
}
