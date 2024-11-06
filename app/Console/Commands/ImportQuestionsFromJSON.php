<?php

namespace App\Console\Commands;

use Harishdurga\LaravelQuiz\Models\Question;
use Harishdurga\LaravelQuiz\Models\QuestionOption;
use Harishdurga\LaravelQuiz\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportQuestionsFromJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-questions-from-json';

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
        $path = storage_path('app/questions.json');
        $json = json_decode(file_get_contents($path));

        // Create topic with exam name
        // Create sub topic with subject name
        $exam = Topic::firstOrCreate([
            'name' => "WAEC",
            'is_active' => true,
        ]);

        $subject = Topic::firstOrCreate([
            'name' => "Use of English",
            'is_active' => true,
        ]);

        $exam->children()->save($subject);

        DB::transaction(function () use ($json, $subject) {
            foreach ($json as $section) {
                $questions = $section->questions;
                $hasPassage = $section->passage || Str::contains($section->instructions, 'passage');

                if ($hasPassage) {
                    // TODO: We will handle passages later in the full blown CBT experience
                    continue;
                }

                foreach ($questions as $question) {
                    $topic = null;

                    if ($question->topic) {
                        $topic = Topic::firstOrCreate([
                            'name' => $question->topic,
                            'is_active' => true,
                        ]);

                        // Attach the topic of the question to the subject
                        $subject->children()->save($topic);
                    }

                    // No options means it's an invalid question
                    if (!$question->options) {
                        continue;
                    }

                    $createdQuestion = Question::create([
                        'name' => preg_replace('/^\d+\.\s*/', '', $question->question),
                        'question_type_id' => 1,
                        'is_active' => true,
                        // Need to add explanation
                        'explanation' => $question->explanation,
                    ]);

                    // Attach topic to question
                    if ($topic) {
                        $createdQuestion->topics()->attach($topic->id);
                    }

                    foreach ($question->options as $option) {
                        QuestionOption::create([
                            'question_id' => $createdQuestion->id,
                            'name' => $option,
                            'is_correct' => $option === $question->answer,
                        ]);
                    }
                }
            }
        });
    }
}
