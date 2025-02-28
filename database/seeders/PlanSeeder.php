<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Perfect for small schools with limited needs.',
                'is_active' => true,
                'price_monthly' => 49.99,
                'price_yearly' => 499.99,
                'trial_days' => 14,
                'max_schools' => 1,
                'max_students' => 100,
                'max_teachers' => 10,
                'max_parents' => 200,
                'features' => [
                    'Student Management',
                    'Teacher Management',
                    'Parent Portal',
                    'Basic Reporting',
                    'Email Support',
                ],
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'description' => 'Ideal for medium-sized schools with growing needs.',
                'is_active' => true,
                'price_monthly' => 99.99,
                'price_yearly' => 999.99,
                'trial_days' => 14,
                'max_schools' => 2,
                'max_students' => 500,
                'max_teachers' => 50,
                'max_parents' => 1000,
                'features' => [
                    'Student Management',
                    'Teacher Management',
                    'Parent Portal',
                    'Advanced Reporting',
                    'Fee Management',
                    'Library Management',
                    'Email & Chat Support',
                    'API Access',
                ],
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Comprehensive solution for large schools and institutions.',
                'is_active' => true,
                'price_monthly' => 199.99,
                'price_yearly' => 1999.99,
                'trial_days' => 14,
                'max_schools' => 5,
                'max_students' => 2000,
                'max_teachers' => 200,
                'max_parents' => 4000,
                'features' => [
                    'Student Management',
                    'Teacher Management',
                    'Parent Portal',
                    'Advanced Reporting',
                    'Fee Management',
                    'Library Management',
                    'Timetable Management',
                    'Attendance Tracking',
                    'Exam Management',
                    'Custom Branding',
                    'Priority Support',
                    'API Access',
                    'Data Export',
                    'Dedicated Account Manager',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
