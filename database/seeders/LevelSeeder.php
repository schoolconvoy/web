<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $school_id = auth()->user()->school->id;

        Level::create(
            [
                'name' => 'CRECHE',
                'shortname' => 'CRECHE',
                'order' => 1,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-SCHOOL ONE',
                'shortname' => 'PG 1',
                'order' => 2,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-SCHOOL TWO',
                'shortname' => 'PG 2',
                'order' => 3,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-SCHOOL THREE',
                'shortname' => 'PG 3',
                'order' => 4,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'PRE-SCHOOL FOUR',
                'shortname' => 'PG 4',
                'order' => 5,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE ONE',
                'shortname' => 'GRADE I',
                'order' => 6,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE TWO',
                'shortname' => 'GRADE II',
                'order' => 7,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE THREE',
                'shortname' => 'GRADE III',
                'order' => 8,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE FOUR',
                'shortname' => 'GRADE IV',
                'order' => 9,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE FIVE',
                'shortname' => 'GRADE V',
                'order' => 10,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'GRADE SIX',
                'shortname' => 'GRADE VI',
                'order' => 11,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL ONE',
                'shortname' => 'JSS 1',
                'order' => 12,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL TWO',
                'shortname' => 'JSS 2',
                'order' => 13,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'JUNIOR SECONDARY SCHOOL THREE',
                'shortname' => 'JSS 3',
                'order' => 14,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL ONE',
                'shortname' => 'SS 1',
                'order' => 15,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL TWO',
                'shortname' => 'SS 2',
                'order' => 16,
                'active' => 1,
                'school_id' => $school_id,
            ],
            [
                'name' => 'SENIOR SECONDARY SCHOOL THREE',
                'shortname' => 'SS 3',
                'order' => 17,
                'active' => 1,
                'school_id' => $school_id,
            ]
        );
    }
}
