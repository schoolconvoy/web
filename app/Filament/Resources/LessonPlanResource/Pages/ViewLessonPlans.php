<?php

namespace App\Filament\Resources\LessonPlanResource\Pages;

use App\Filament\Resources\LessonPlanResource;
use App\Models\LessonPlan;
use App\Models\Week;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

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

        if(!$this->record) {
            return;
        }

        if ($lastFragment === 'mine') {
            $this->lessonPlans = Week::find($this->record->id)
                                        ->lessonPlans()
                                        ->orderBy('created_at', 'desc')
                                        ->where('teacher_id', auth()->id())
                                        ->with('teacher')
                                        ->get();
            return;
        }

        $user = auth()->user();
        $isAdmin = $user->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE
        ]);

        // For admins/principals, show all lesson plans
        if ($isAdmin) {
            if ($lastFragment === 'view' || $lastFragment == $this->record->id) {
                $this->lessonPlans = Week::find($this->record->id)
                                        ->lessonPlans()
                                        ->orderBy('created_at', 'desc')
                                        ->with('teacher')
                                        ->get();
                return;
            }

            if ($lastFragment != $this->record->id) {
                $this->lessonPlans = Week::find($this->record->id)
                                        ->lessonPlans()
                                        ->with('teacher')
                                        ->orderBy('created_at', 'desc')
                                        ->where('status', $lastFragment)
                                        ->get();
                return;
            }
        } else {
            // For regular teachers, only show their own lesson plans and approved ones
            if ($lastFragment === 'view' || $lastFragment == $this->record->id) {
                $this->lessonPlans = Week::find($this->record->id)
                                        ->lessonPlans()
                                        ->where(function($query) {
                                            $query->where('teacher_id', auth()->id())
                                                  ->orWhere('status', LessonPlan::APPROVED);
                                        })
                                        ->orderBy('created_at', 'desc')
                                        ->with('teacher')
                                        ->get();
                return;
            }

            if ($lastFragment != $this->record->id) {
                $this->lessonPlans = Week::find($this->record->id)
                                        ->lessonPlans()
                                        ->where(function($query) {
                                            $query->where('teacher_id', auth()->id())
                                                  ->orWhere('status', LessonPlan::APPROVED);
                                        })
                                        ->where('status', $lastFragment)
                                        ->orderBy('created_at', 'desc')
                                        ->with('teacher')
                                        ->get();
                return;
            }
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

    public function getRecord(): Model
    {
        // return a 404 if record is not found
        if (!$this->record) {
            abort(404);
        }

        return $this->record;
    }

    public function mount(int | string $record): void
    {
        // parent::mount($record);

        $this->record = Week::find($record);

        $this->getLessonPlans();
    }
}
