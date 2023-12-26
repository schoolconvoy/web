<?php

namespace App\Filament\Resources\CBTResource\Pages;

use App\Filament\Resources\CBTResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Harishdurga\LaravelQuiz\Models\QuizAttempt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Harishdurga\LaravelQuiz\Models\QuizAttemptAnswer;

class ViewRevision extends ViewRecord
{
    protected static string $resource = CBTResource::class;
    protected static string $view = 'filament.resources.c-b-t-resource.pages.review';

    public $attempt;
    public $score;
    public $answers = [];

    public function getTitle(): string | Htmlable
    {
        return $this->record->name;
    }

    public function getHeading(): string
    {
        return $this->record->name;
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $attemptId = request()->get('attempt');

        $this->attempt = QuizAttempt::find($attemptId);
        $this->score = request()->get('score');

        Log::debug('Attempt is ' . print_r($this->attempt, true));

        $this->record->load(['topics', 'questions.question']);

        $this->authorizeAccess();

        if (! $this->hasInfolist()) {
            $this->fillForm();
        }
    }
}
