<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
            if(Role::findByName($role) !== null)
            {
                continue;
            }

            Role::create([
                'name' => $role
            ]);
        }
    }
}
