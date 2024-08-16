<?php

namespace App\Livewire\LessonPlan;

use App\Models\LessonPlanReview;
use App\Models\LessonPlan;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use App\Events\LessonPlanReviewed;
use Illuminate\Support\Facades\Log;

class ReviewLessonPlan extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public LessonPlan $lessonPlan;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('comment')
                        ->label('Comment')
                        ->helperText('Describe the change to be made')
                        ->autosize()
            ])
            ->statePath('data')
            ->model(LessonPlanReview::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $data['status'] = 0;
        $data['reviewed_by'] = auth()->id();
        $data['lesson_plan_id'] = $this->lessonPlan->id;

        $record = LessonPlanReview::create($data);

        // Set lesson plan status to pending
        $this->lessonPlan->update(['status' => LessonPlan::IN_REVIEW]);

        Notification::make()
            ->title('Correction submitted')
            ->body('We have notified this teacher by mail of your review.')
            ->success()
            ->send();

        $this->form->model($record)->saveRelationships();

        $this->form->fill();

        $reviewedEmail = LessonPlanReviewed::dispatch($record);

        $this->dispatch('close-modal', id: 'reviewLessonPlanModal');
    }

    public function render(): View
    {
        return view('livewire.lesson-plan.review-lesson-plan');
    }
}
