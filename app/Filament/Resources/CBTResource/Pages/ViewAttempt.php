<?php

namespace App\Filament\Resources\CBTResource\Pages;

use App\Filament\Resources\CBTResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Harishdurga\LaravelQuiz\Models\QuizAttempt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Harishdurga\LaravelQuiz\Models\QuizAttemptAnswer;

class ViewAttempt extends ViewRecord
{
    protected static string $resource = CBTResource::class;
    protected static string $view = 'filament.resources.c-b-t-resource.pages.do-quiz';

    public $attempt;
    public $answers = [];

    public function getTitle(): string | Htmlable
    {
        return $this->record->name;
    }

    public function getHeading(): string
    {
        return $this->record->name;
    }

    public function handleSubmission()
    {

        Log::debug('All answers submitted are ' . print_r($this->only('answers'), true));

        foreach ($this->answers as $question_id => $question_option_id) {
            Log::debug('answer is ' . print_r([$question_id, $this->attempt->id, $question_option_id], true));
            QuizAttemptAnswer::create(
                [
                    'quiz_attempt_id' => $this->attempt->id,
                    'quiz_question_id' => $question_id,
                    'question_option_id' => $question_option_id,
                ]
            );
        }

        Log::debug('Score calculated is ' . print_r($this->attempt->calculate_score(), true));
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->record->load(['topics', 'questions.question']);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }

        $attempts = $this->record->attempts()
                                ->where('participant_id', auth()->user()->id)
                                ->where('participant_type', get_class(auth()->user()))
                                ->count();
        $max_attempts = $this->record->max_attempts;

        // abort_unless($attempts < $max_attempts, 403);

        $attempt = QuizAttempt::create([
            'quiz_id' => $this->record->id,
            'participant_id' => auth()->user()->id,
            'participant_type' => get_class(auth()->user()),
        ]);

        $this->attempt = $attempt;
    }
}
