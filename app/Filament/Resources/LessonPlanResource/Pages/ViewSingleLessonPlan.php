<?php

namespace App\Filament\Resources\LessonPlanResource\Pages;

use App\Events\LessonPlanApproved;
use App\Filament\Resources\LessonPlanResource;
use App\Models\LessonPlan;
use App\Models\LessonPlanReview;
use App\Models\Week;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\LessonPlanReviewUpdated;
use App\Models\User;

class ViewSingleLessonPlan extends ViewRecord
{
    protected static string $resource = LessonPlanResource::class;
    protected static string $view = 'filament.resources.lesson-plans.pages.view-single-plans';
    public $plan;

    public function getBreadcrumb(): string
    {
        return $this->plan->name;
    }

    public function getTitle(): string
    {
        return $this->plan->name . ": " . $this->plan->session->year . " (" . $this->plan->term->name . ")";
    }

    public function getLessonPlan()
    {
        // Get plan from the url fragment
        $url = explode('/', url()->current());
        $id = end($url);

        $this->plan = LessonPlan::find($id);

        return $this->plan;
    }

    public function approvePlan() {
        $id = $this->plan->id;

        $lessonPlan = LessonPlan::find($id);

        $lessonPlan->teacher;
        $lessonPlan->week;
        $lessonPlan->approved_by = auth()->id();
        $lessonPlan->approved_at = now();
        $lessonPlan->status = LessonPlan::APPROVED;

        $lessonPlan->save();

        Notification::make()
            ->title('Lesson Plan Approved')
            ->body('This lesson plan has been approved and the teacher is notified.')
            ->success()
            ->send();

        LessonPlanApproved::dispatch($lessonPlan);
    }

    public function updateStatus($reviewId) {
        $review = LessonPlanReview::find($reviewId);

        $review->status = !$review->status;

        Notification::make()
            ->title('Correction resolved')
            ->body('This correction has been marked as done and the reviewer is notified.')
            ->success()
            ->send();

        $review->save();

        $reviewer = User::find($review->reviewed_by);
        $reviewer = [$reviewer];

        $users = User::role([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE
        ])->get();

        // Dispatch event to notify the reviewer
        NotificationFacade::send(count($reviewer) > 0 ? $reviewer : $users, new LessonPlanReviewUpdated($review));
    }

    public function downloadTrigger()
    {
        return response()->download(storage_path('app/public/' . $this->plan->files));
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getLessonPlan();
    }
}
