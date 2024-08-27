<?php

namespace App\Livewire\PaymentReminder;

use App\Models\LessonPlanTopic;
use App\Models\Session;
use App\Models\LessonPlan;
use App\Models\PaymentReminder;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use App\Notifications\LessonPlanCreated as LessonPlanCreatedNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;

class CreatePaymentReminder extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public $parentId;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $wards = User::find($this->parentId)->wards;

        return $form
            ->schema([
                Grid::make()->columns(2)->schema([
                    Select::make('student_id')
                        ->helperText('Select the student to load their fees')
                        ->label('Student')
                        ->options($wards->pluck('firstname', 'id')->toArray())
                        ->live()
                        ->required(),
                    Select::make('fees')
                        ->multiple()
                        ->required()
                        ->searchable()
                        ->options(function (Get $get) {
                            $student = $get('student_id');

                            $fees = User::find($student)?->fees?->pluck('name', 'id');

                            return $fees ? $fees->toArray() : [];
                        }),
                ]),
                Textarea::make('message')
                    ->required()
                    ->rows(3)
                    ->maxLength(255)
                    ->helperText('Write a short message as a reminder. Max. 255 characters'),

            ])
            ->statePath('data')
            ->model(PaymentReminder::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        // If a reminder was created less than a week ago, reject the request
        $lastReminder = PaymentReminder::where('student_id', $data['student_id'])
                                        ->where('created_at', '>', now()->subWeek())
                                        ->first();

        if ($lastReminder) {
            Notification::make()
                ->title('You cannot create a reminder')
                ->body('You cannot create a reminder for this student as you have created one less than a week ago.')
                ->danger()
                ->send();

            $this->dispatch('close-modal', id: 'create-reminder-modal');

            return;
        }

        // Create reminder
        $data['sent_by'] = auth()->id();
        $data['parent_id'] = $this->parentId;
        $fees = $data['fees'];
        unset($data['fees']);

        $record = PaymentReminder::create($data);

        $parent = $this->parentId;

        $record->fees()->saveMany($fees);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Reminder created')
            ->body('The reminder has been created successfully.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'create-reminder-modal');

        // Update the UI with the newly created lesson
        $this->dispatch('reminder-created', id: $record->id);
    }

    public function render(): View
    {
        return view('livewire.payment-reminder.create-reminder');
    }
}
