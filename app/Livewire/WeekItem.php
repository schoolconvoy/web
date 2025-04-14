<?php

namespace App\Livewire;

use App\Filament\Resources\LessonPlanResource;
use App\Filament\Resources\LessonPlanResource\Pages\ListLessonPlans;
use App\Models\LessonPlan;
use App\Models\User;
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

    public function mount($week, $lessonPlansCount, $type)
    {
        $this->week = $week;
        $this->type = $type;
        $this->weekId = $week->id;

        // Calculate the correct count based on user role and type
        $user = auth()->user();
        $isAdmin = $user->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE
        ]);

        $query = $week->lessonPlans();

        if (!$isAdmin) {
            // For regular teachers, only count their own lesson plans and approved ones
            $query->where(function($q) {
                $q->where('teacher_id', auth()->id())
                  ->orWhere('status', LessonPlan::APPROVED);
            });
        }

        // Apply additional filtering based on type
        if ($type === 'pending') {
            $query->where('status', LessonPlan::AWAITING_REVIEW);
        } elseif ($type === 'approved') {
            $query->where('status', LessonPlan::APPROVED);
        } elseif ($type === 'mine') {
            $query->where('teacher_id', auth()->id());
        }

        $this->lessonPlansCount = $query->count();
    }

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
