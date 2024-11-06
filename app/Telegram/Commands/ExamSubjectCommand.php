<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;

/**
 * This command can be triggered in two ways:
 */
class ExamSubjectCommand extends Command
{
    protected string $name = 'examsubject';
    protected string $description = 'Set the subject for the exam you want to take a quiz for. Example: /examsubject Mathematics  or /examsubject WAEC Mathematics';
    protected string $pattern = '{examsubject: .*}';

    const SUBJECTS = [
        'waec' => [
            'mathematics' => 'Mathematics',
            'english' => 'English',
            'physics' => 'Physics',
            'chemistry' => 'Chemistry',
            'biology' => 'Biology',
            'agriculture' => 'Agriculture',
            'economics' => 'Economics',
            'commerce' => 'Commerce',
            'accounting' => 'Accounting',
            'government' => 'Government',
            'geography' => 'Geography',
            'history' => 'History',
            'civic-education' => 'Civic Education',
            'literature' => 'Literature',
            'further-mathematics' => 'Further Mathematics',
            'french' => 'French',
            'spanish' => 'Spanish',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'home-economics' => 'Home Economics',
            'food-nutrition' => 'Food and Nutrition'
        ],
        'jamb' => [
            'mathematics' => 'Mathematics',
            'use-of-english' => 'Use of English',
            'physics' => 'Physics',
            'chemistry' => 'Chemistry',
            'biology' => 'Biology',
            'agriculture' => 'Agriculture',
            'economics' => 'Economics',
            'commerce' => 'Commerce',
            'accounting' => 'Accounting',
            'government' => 'Government',
            'geography' => 'Geography',
            'history' => 'History',
            'civic-education' => 'Civic Education',
            'literature' => 'Literature',
        ],
        'neco' => [
            'mathematics' => 'Mathematics',
            'english' => 'English',
            'physics' => 'Physics',
            'chemistry' => 'Chemistry',
            'biology' => 'Biology',
            'agriculture' => 'Agriculture',
            'economics' => 'Economics',
            'commerce' => 'Commerce',
            'accounting' => 'Accounting',
            'government' => 'Government',
            'geography' => 'Geography',
            'history' => 'History',
            'civic-education' => 'Civic Education',
            'literature' => 'Literature',
            'further-mathematics' => 'Further Mathematics',
            'french' => 'French',
            'spanish' => 'Spanish',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'home-economics' => 'Home Economics',
            'food-nutrition' => 'Food and Nutrition'
        ],
        'bece' => [
            'mathematics' => 'Mathematics',
            'english' => 'English',
            'basic-science' => 'Basic Science',
            'basic-technology' => 'Basic Technology',
            'civic-education' => 'Civic Education',
            'computer-studies' => 'Computer Studies',
            'physical-health-education' => 'Physical Health Education',
            'agricultural-science' => 'Agricultural Science',
            'home-economics' => 'Home Economics',
            'business-studies' => 'Business Studies',
            'french' => 'French',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'literature' => 'Literature',
            'history' => 'History',
            'geography' => 'Geography',
            'cultural-and-creative-arts' => 'Cultural and Creative Arts',
            'music' => 'Music',
            'visual-arts' => 'Visual Arts',
            'business-studies' => 'Business Studies',
            'french' => 'French',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'literature' => 'Literature',
            'history' => 'History',
            'geography' => 'Geography',
            'cultural-and-creative-arts' => 'Cultural and Creative Arts',
            'music' => 'Music',
            'visual-arts' => 'Visual Arts',
        ],
        'common-entrance' => [
            'mathematics' => 'Mathematics',
            'english' => 'English',
            'basic-science' => 'Basic Science',
            'basic-technology' => 'Basic Technology',
            'civic-education' => 'Civic Education',
            'computer-studies' => 'Computer Studies',
            'physical-health-education' => 'Physical Health Education',
            'agricultural-science' => 'Agricultural Science',
            'home-economics' => 'Home Economics',
            'business-studies' => 'Business Studies',
            'french' => 'French',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'literature' => 'Literature',
            'history' => 'History',
            'geography' => 'Geography',
            'cultural-and-creative-arts' => 'Cultural and Creative Arts',
            'music' => 'Music',
            'visual-arts' => 'Visual Arts',
            'business-studies' => 'Business Studies',
            'french' => 'French',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'literature' => 'Literature',
            'history' => 'History',
            'geography' => 'Geography',
            'cultural-and-creative-arts' => 'Cultural and Creative Arts',
            'music' => 'Music',
            'visual-arts' => 'Visual Arts',
        ],
        'nabteb' => [
            'mathematics' => 'Mathematics',
            'english' => 'English',
            'physics' => 'Physics',
            'chemistry' => 'Chemistry',
            'biology' => 'Biology',
            'agriculture' => 'Agriculture',
            'economics' => 'Economics',
            'commerce' => 'Commerce',
            'accounting' => 'Accounting',
            'government' => 'Government',
            'geography' => 'Geography',
            'history' => 'History',
            'civic-education' => 'Civic Education',
            'literature' => 'Literature',
            'further-mathematics' => 'Further Mathematics',
            'french' => 'French',
            'spanish' => 'Spanish',
            'yoruba' => 'Yoruba',
            'igbo' => 'Igbo',
            'hausa' => 'Hausa',
            'crk' => 'CRK',
            'irk' => 'IRK',
            'home-economics' => 'Home Economics',
            'food-nutrition' => 'Food and Nutrition'
        ]
    ];

    public function handle()
    {
        # Get the username argument if the user provides,
        # (optional) fallback to username from Update object as the default.
        $subject = $this->argument('examsubject');
        $keyboard = Keyboard::make()
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->inline();

        if ($subject) {
            [$exam, $subject] = explode(' ', $subject, 2);
        } else {
            $exam = 'jamb';
        }

        $row = array();

        $exam_subjects = self::SUBJECTS[$exam];

        foreach($exam_subjects as $key => $subject)
        {
            $row[] = Keyboard::inlineButton([
                'text' => $subject,
                'callback_data' => json_encode(['subject' => $key]),
            ]);

            if (count($row) === 2) {
                $keyboard->row($row);
                $row = array();
            }
        }

        $this->replyWithMessage([
            'text' => "Please choose your preferred subject in this exam",
            'reply_markup' => $keyboard
        ]);
    }
}
