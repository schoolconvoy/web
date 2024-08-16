<?php

namespace App\Filament\Resources\LessonPlanResource\Pages;

use App\Filament\Resources\LessonPlanResource;
use App\Models\LessonPlan;
use App\Models\Week;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ViewLessonPlans extends ViewRecord
{
    protected static string $resource = LessonPlanResource::class;
    protected static string $view = 'filament.resources.lesson-plans.pages.view-plans';

    public $lessonPlans = [];
    public $week;

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function getTitle(): string
    {
        return $this->record->name . ": " . $this->record->session->year . " (" . $this->record->term->name . ")";
    }

    /**
     * Retrieve lessons plans based on the last fragment of the URL.
     */
    public function getLessonPlans()
    {
        // Get view url and split it into an array
        $url = explode('/', url()->current());
        // Get the last fragment of the URL
        $lastFragment = end($url);

        Log::debug("Last fragment " . $lastFragment . " record id " . $this->record->id);

        if ($lastFragment === 'mine') {
            $this->lessonPlans = Week::find($this->record->id)->lessonPlans()->orderBy('created_at', 'desc')->where('teacher_id', auth()->id())->get();
            Log::debug("Mine Lesson plans " . print_r($this->lessonPlans, true));

            return;
        }

        // A hack to get all lesson plans when no status is provided. || Comparing string with int, therefore loose comparison
        if ($lastFragment === 'view' || $lastFragment == $this->record->id) {
            $this->lessonPlans = Week::find($this->record->id)->lessonPlans()->orderBy('created_at', 'desc')->get();
            Log::debug("View Lesson plans " . print_r($this->lessonPlans, true));

            return;
        }

        if ($lastFragment != $this->record->id) {
            $this->lessonPlans = Week::find($this->record->id)->lessonPlans()->orderBy('created_at', 'desc')->where('status', $lastFragment)->get();
            Log::debug("Others Lesson plans " . print_r($this->lessonPlans, true));

            return;
        }
    }

    #[On('lesson-created')]
    public function updateLessonPlansList($id)
    {
        $record = LessonPlan::find($id);
        $this->lessonPlans = collect($this->lessonPlans)->prepend($record);
    }

    public function teachers()
    {
        return User::teachers();
    }

    public function mount(int | string $record): void
    {
        // parent::mount($record);

        $this->record = Week::find($record);

        $this->getLessonPlans();
    }
}
