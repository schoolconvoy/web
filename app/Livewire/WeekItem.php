<?php

namespace App\Livewire;

use App\Filament\Resources\LessonPlanResource;
use App\Filament\Resources\LessonPlanResource\Pages\ListLessonPlans;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class WeekItem extends Component
{
    /** @var week The week object passed in a loop */
    public $week;
    /** @var lessonPlansCount The number of lesson plans for the week */
    public $lessonPlansCount;
    /** @var type The type of lesson plans to display */
    public $type;
    public $lessonPlanUrl = null;
    public $weekId;

    public function openEditModal($weekId)
    {
        // Pass weekId to the EditWeek livewire component
        $this->dispatch('open-week-edit-modal', id: $weekId);
    }

    public function render()
    {
        return view('livewire.week-item');
    }
}
