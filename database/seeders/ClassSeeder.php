<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\Classes;
use Illuminate\Support\Facades\Log;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school_id = 1; //auth()->user()->school->id;

        $classes = [
            [
                'name' => 'CRECHE',
                'shortname' => 'CRECHE',
                'order' => 1,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-SCHOOL ONE',
                'shortname' => 'PG 1',
                'order' => 2,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-NURSERY',
                'shortname' => 'PG 2',
                'order' => 3,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'NURSERY',
                'shortname' => 'PG 3',
                'order' => 4,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'RECEPTION',
                'shortname' => 'PG 4',
                'order' => 5,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE ONE',
                'shortname' => 'GRADE I',
                'order' => 6,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE TWO',
                'shortname' => 'GRADE II',
                'order' => 7,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE THREE',
                'shortname' => 'GRADE III',
                'order' => 8,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE FOUR',
                'shortname' => 'GRADE IV',
                'order' => 9,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE FIVE',
                'shortname' => 'GRADE V',
                'order' => 10,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE SIX',
                'shortname' => 'GRADE VI',
                'order' => 11,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL ONE',
                'shortname' => 'JSS 1',
                'order' => 12,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL TWO',
                'shortname' => 'JSS 2',
                'order' => 13,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL THREE',
                'shortname' => 'JSS 3',
                'order' => 14,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL ONE',
                'shortname' => 'SS 1',
                'order' => 15,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL TWO',
                'shortname' => 'SS 2',
                'order' => 16,
                'status' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL THREE',
                'shortname' => 'SS 3',
                'order' => 17,
                'status' => 1,
                'school_id' => $school_id,
            ]
        ];

        foreach($classes as $class)
        {

            if (Level::where('name', $class['name'])->exists()) {
                $level = Level::where('name', $class['name'])->first();
            } else {
                $level = Level::create($class);
            }

            $class = Classes::create([
                'name' => $class['name'],
                'level_id' => $level->id,
                'school_id' => $school_id,
            ]);
        }

        Log::debug('Info: Created ' . print_r(Classes::count() . ' classes' . ' And ' . Level::count() . ' levels ', true));
    }
}
