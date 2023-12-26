<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                "name" => "Agriculture Science",
                'shortname' => 'Agric Science',
                "code" => "EDU-111",
            ],
            [
                "name" => "Basic Science & Technology",
                'shortname' => 'Basic Science',
                "code" => "EDU-102",
            ],
            [
                "name" => "Business Studies",
                'shortname' => 'Business Studies',
                "code" => "EDU-104",
            ],
            [
                "name"=> "Civic Education",
                'shortname' => 'Civic Edu',
                "code"=> "EDU-105",
            ],
            [
                "name"=> "Cultural Religion Studies",
                'shortname' => 'CRS',
                "code"=> "RNV-106",
            ],
            [
                "name"=> "English Language",
                'shortname' => 'English',
                "code"=> "EDU-107",
            ],
            [
                "name"=> "Home Economics",
                'shortname' => 'Home Econs',
                "code"=> "EDU-108"
            ],
            [
                "name"=> "Information Technology",
                "code"=> "CIS-102",
                'shortname' => 'IT',
            ],
            [
                "name"=> "Mathematics",
                "code"=> "EDU-109",
                'shortname' => 'Maths',
            ],
            [
                "name"=> "Physical Health Education",
                "code"=> "EDU-110",
                'shortname' => 'PHE',
            ],
            [
                "name"=> "Security Education",
                "code"=> "CIS-103",
                'shortname' => 'Security Education',
            ],
            [
                "name"=> "Social Studies",
                "code"=> "EDU-112",
                'shortname' => 'Social Studies',
            ],
            [
                "name"=> "Vocational Aptitude",
                "code"=> "EDI113",
                'shortname' => 'Vocational Aptitude',
            ],
            [
                "name"=> "Verbal Reasoning",
                "code"=> "EDU-114",
                'shortname' => 'Verbal Reasoning',
            ],
            [
                "name"=> "Quantitative Reasoning",
                "code"=> "QRT-115",
                'shortname' => 'Quantitative Reasoning',
            ],
            [
                "name"=> "Fine Art",
                "code"=> "EDU-116",
                'shortname' => 'Fine Art',
            ],
            [
                "name"=> "Yoruba",
                "code"=> "EDU-117",
                'shortname' => 'Yor',
            ],
            [
                "name"=> "French",
                "code"=> "EDU-118",
                'shortname' => 'French',
            ],
            [
                "name"=> "Music",
                "code"=> "EDU-119",
                'shortname' => 'Music',
            ],
            [
                "name"=> "Custom & Tradition",
                "code"=> "EDU-120",
                'shortname' => 'CT',
            ],
            [
                "name"=> "Performing Art and Entertainment",
                "code"=> "PAE-121",
                'shortname' => 'PAE',
            ],
            [
                "name"=> "Physical, Social and Emotional Development",
                "code"=> "PSE-122",
                'shortname' => 'PSE',
            ],
            [
                "name"=> "Understanding the World",
                "code"=> "EDU-123",
                'shortname' => 'UW',
            ],
            [
                "name"=> "Communication and Language",
                "code"=> "EDU-124",
                'shortname' => 'C&L',
            ],
            [
                "name"=> "Numeracy",
                "code"=> "EDU-125",
                'shortname' => 'Numeracy',
            ],
            [
                "name"=> "Literacy",
                "code"=> "EDU-126",
                'shortname' => 'Literacy',
            ],
            [
                "name"=> "Expressive Art & design",
                "code"=> "EAD-127",
                'shortname' => 'EAD',
            ],
            [
                "name"=> "Civic Education",
                "code"=> "EDU-128",
                'shortname' => 'Civic',
            ],
            [
                "name"=> "Cultural & Creative Art",
                "code"=> "EDU-129",
                'shortname' => 'CCA',
            ],
            [
                "name"=> "Physics",
                "code"=> "EDU-130",
                'shortname' => 'PHY',
            ],
            [
                "name"=> "Chemistry",
                "code"=> "EDU-131",
                'shortname' => 'CHM',
            ],
            [
                "name"=> "Biology",
                "code"=> "BIO-131",
                'shortname' => 'BIO',
            ],
            [
                "name"=> "Further Mathematics",
                "code"=> "EDU-133",
                'shortname' => 'FMaths',
            ],
            [
                "name"=> "Literature in English",
                "code"=> "EDU-134",
                'shortname' => 'Lit-In-English',
            ],
            [
                "name"=> "Marketing",
                "code"=> "EDU-135",
                'shortname' => 'Marketing',
            ],
            [
                "name"=> "ICT",
                "code"=> "EDU-137",
                'shortname' => 'ICT',
            ],
            [
                "name"=> "Government",
                "code"=> "EDU-138",
                'shortname' => 'Govt',
            ],
            [
                "name"=> "Economics",
                "code"=> "EDU-136",
                'shortname' => 'ECN',
            ],
            [
                "name"=> "Geography",
                "code"=> "EDU-139",
                'shortname' => 'Geo',
            ],
            [
                "name"=> "Physical Development",
                "code"=> "PD-128",
                'shortname' => 'Physical Dev.',
            ]
        ];

        foreach($subjects as $subject)
        {
            Subject::create($subject);
        }
    }
}
