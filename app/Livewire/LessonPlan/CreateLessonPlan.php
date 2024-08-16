<?php

namespace App\Livewire\LessonPlan;

use App\Models\LessonPlanTopic;
use App\Models\Session;
use App\Models\LessonPlan;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use App\Notifications\LessonPlanCreated as LessonPlanCreatedNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Models\User;

class CreateLessonPlan extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $week;

    public function mount(): void
    {
        $this->form->fill();
        Log::debug(print_r($this->week, true));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->columns(2)->schema([
                    TextInput::make('name')
                        ->required()
                        ->helperText('Enter the title of the lesson plan'),
                    Select::make('class_id')
                        ->helperText('Select the class')
                        ->relationship('class', 'name')
                        ->preload()
                        ->searchable()
                        ->required()
                ]),
                Select::make('subject_id')
                    ->helperText('Select the subject')
                    ->relationship('subject', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                // Should be relationship with subject
                Select::make('lesson_plan_topic_id')
                    ->helperText('Select the lesson plan topic')
                    ->preload()
                    ->options(LessonPlanTopic::pluck('name', 'id'))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->required(),
                Grid::make()->columns(2)->schema([
                    TextInput::make('period')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(8)
                        ->helperText('Enter the period of the lesson plan'),
                    Select::make('duration')
                        ->required()
                        ->options([
                            '30 minutes' => '30 minutes',
                            '1 hour' => '1 hour',
                            '2 hours' => '2 hours',
                            '3 hours' => '3 hours',
                        ])
                        ->searchable()
                        ->helperText('Enter the duration of the lesson plan'),
                ]),
                TextArea::make('objectives')
                    ->required()
                    ->rows(3)
                    ->maxLength(255)
                    ->helperText('Enter the objectives of the lesson plan. Max. 255 characters'),
                FileUpload::make('files')
                    ->label('Upload lesson file')
                    ->required(),

            ])
            ->statePath('data')
            ->model(LessonPlan::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $session = Session::active();

        $data['session_id'] = $session->id;
        $data['term_id'] = $session->terms->where('active', true)->first()->id;
        $data['teacher_id'] = auth()->id();
        $data['status'] = LessonPlan::AWAITING_REVIEW;

        $data['week_id'] = $this->week;

        Log::info(print_r($data, true));

        $record = LessonPlan::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Lesson plan created successfully')
            ->body('Your lesson plan has been created successfully and is awaiting review.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'create-lesson-plan-modal');
        // Update the UI with the newly created lesson
        $this->dispatch('lesson-created', id: $record->id);

        $roles = [
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE
        ];

        if ($record->teacher->isHighSchool()) {
            $roles[] = User::$HIGH_PRINCIPAL_ROLE;
        } else {
            $roles[] = User::$ELEM_PRINCIPAL_ROLE;
        }

        $users = User::role($roles)->get();

        // Dispatch event to notify the reviewer
        NotificationFacade::send($users, new LessonPlanCreatedNotification($record));
    }

    public function render(): View
    {
        return view('livewire.lesson-plan.create-lesson-plan');
    }
}
