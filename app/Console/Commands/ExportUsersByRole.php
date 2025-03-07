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
        fputcsv($handle, ['school', 'email', 'name', 'type', 'password']);

        // Loop through users and write each row
        foreach ($users as $user) {
            $plain_password = $this->generateMemorablePassword();
            $user->password = Hash::make($plain_password);
            $user->save();

            fputcsv($handle, [
                $role === "student" ? ($user->isHighSchool() ? 'High School' : 'Elementary School') : 'General',
                $user->email,
                $user->fullname,
                $role,
                $plain_password
            ]);
        }

        // Close file handle
        fclose($handle);

        // Inform the user via console
        $this->info("Exported " . $users->count() . " user(s) with role '{$role}' to: {$filePath}");

        return 0; // success exit code
    }

    public function generateMemorablePassword($numWords = 3, $separator = '-')
    {
        // Example small dictionary. Ideally, use a larger set of words.
        $wordList = [
            'apple','banana','cherry','dragon','eagle','forest','globe','happy',
            'index','jungle','kite','lemon','monkey','number','ocean','purple',
            'quiet','river','summer','tiger','unicorn','vivid','whale','xenon',
            'yellow','zebra'
        ];

        // Shuffle or randomly pick words
        $words = [];
        for ($i = 0; $i < $numWords; $i++) {
            $words[] = $wordList[array_rand($wordList)];
        }

        // Combine the words with a separator
        $passphrase = implode($separator, $words);

        // Optionally, add a random digit/symbol to increase complexity
        $passphrase .= rand(0, 9);

        return $passphrase;
    }
}
