<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Result;
use App\Models\Classes;
use App\Models\Level;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Session;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;

class BulkResultsEntry extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-arrow-up-on-square-stack';
    protected static string $view = 'filament.pages.bulk-results-entry';

    // 1) Filter properties
    public ?int $sessionId = null;
    public ?int $termId = null;
    public ?int $classId = null;
    public ?int $subjectId = null;

    // e.g. $results[123]['ca1'] = 40, $results[123]['ca2'] = 35, ...
    public array $results = [];

    /**
     * Livewire lifecycle method, runs on page load.
     */
    public function mount(): void
    {
        // Optionally set defaults, if needed
        $this->sessionId = null;
        $this->termId = null;
        $this->classId = null;
        $this->subjectId = null;
    }

    public function saveBulkResults()
    {
        // Validate
        $validated = $this->validate([
            'sessionId'  => 'required|exists:sessions,id',
            'termId'     => 'required|exists:terms,id',
            'classId'    => 'required|exists:classes,id',
            'subjectId'  => 'required|exists:subjects,id',
            'results'    => 'required|array',
            'results.*.ca1'        => 'nullable|numeric',
            'results.*.ca2'        => 'nullable|numeric',
            'results.*.exam_score' => 'nullable|numeric',
        ]);

        if (! $validated) {
            return Notification::make()
                ->title('Invalid data!')
                ->error()
                ->send();
        }

        // Loop through each student’s result data
        foreach ($this->results as $studentId => $data) {
            // You can optional null-coalesce them:
            $ca1 = $data['ca1'] ?? 0;
            $ca2 = $data['ca2'] ?? 0;
            $exam = $data['exam_score'] ?? 0;

            Result::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id'   => $this->classId,
                    'term_id'    => $this->termId,
                    'subject_id' => $this->subjectId,
                    'session_id' => $this->sessionId,
                    'created_by' => auth()->id(),
                ],
                [
                    'ca_1'        => $ca1,
                    'ca_2'        => $ca2,
                    'exam_score'  => $exam,
                    'total_score' => $ca1 + $ca2 + $exam,
                ]
            );
        }

        return Notification::make()
                ->title('Bulk results saved!')
                ->success()
                ->send();
    }

    #[Computed]
    public function getFilteredStudentsProperty()
    {
        // Return empty if not all filters chosen:
        if (! $this->sessionId || ! $this->termId || ! $this->classId || ! $this->subjectId) {
            return collect();
        }

        return User::role(User::$STUDENT_ROLE)
                    ->where('class_id', $this->classId)
                    ->get();
    }

    public function filterStudents()
    {
        // If any filter is missing, clear everything
        if (
            ! $this->sessionId ||
            ! $this->termId ||
            ! $this->classId ||
            ! $this->subjectId
        ) {
            $this->results = [];
            return;
        }

        // 1) Load existing results from DB for the chosen filters:
        $existingResults = Result::where([
            'session_id' => $this->sessionId,
            'term_id'    => $this->termId,
            'class_id'   => $this->classId,
            'subject_id' => $this->subjectId,
        ])->get();

        // 2) Convert them to an array keyed by student_id
        // so $this->results[123]['ca1'] = 40, etc.
        $this->results = $existingResults->keyBy('student_id')->map(function ($result) {
            return [
                'ca1'        => $result->ca_1,
                'ca2'        => $result->ca_2,
                'exam_score' => $result->exam_score,
            ];
        })->toArray();
    }

    public function updated($propertyName)
    {
        // If the property is one of sessionId, termId, classId, subjectId,
        // run filterStudents() if all are set, or clear results if not.
        if (in_array($propertyName, ['sessionId', 'termId', 'classId', 'subjectId'])) {
            $this->filterStudents();
        }
    }

    protected function getViewData(): array
    {
        return [
            'classes' => Classes::highSchool()->get(),
            'students' => $this->filteredStudents,
            'terms' => Term::all(),
            'subjects' => Subject::whereHas('level', function ($query) {
                $query->where('order', '>', 11);
            })->get(),
            'sessions' => Session::all(),
        ];
    }

    /**
    * If `canAccess()` returns false, users won't see or access this resource.
    */
    public static function canAccess(): bool
    {
        // Only show to high school teachers
        return auth()->check() && auth()->user()->isHighSchool();
    }
}
