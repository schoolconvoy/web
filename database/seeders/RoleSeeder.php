<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(User::getRoles() as $role)
        {
            try {
                Role::create([
                    'name' => $role
                ]);
            } catch (\Throwable $th) {
                // throw $th;
                Log::debug('Role already exists ' . $role);
            }
        }
    }
}
