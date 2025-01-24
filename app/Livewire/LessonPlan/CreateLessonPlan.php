<?php

namespace App\Livewire\LessonPlan;

use App\Events\LessonPlanCreated;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CreateLessonPlan extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $createData = ['attachments' => null];
    public $week;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('attachments')
                ->label('Upload lesson file')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/x-pdf',
                    'application/vnd.pdf',
                    'application/vnd.adobe.pdfxml',
                    'application/pdfa'
                ])
                ->required(),
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
                Select::make('topics')
                    ->relationship('topics', 'name')
                    ->label('Select a topic')
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->live(true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            })
                            ->required(),
                        TextInput::make('slug')
                            ->required()
                    ])
                    ->searchable(),
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
                            '40 minutes' => '40 minutes',
                            '80 minutes' => '80 minutes',
                            '120 minutes' => '120 minutes'
                        ])
                        ->searchable()
                        ->helperText('Enter the duration of the lesson plan'),
                ]),
                Textarea::make('objectives')
                    ->required()
                    ->rows(3)
                    ->maxLength(255)
                    ->helperText('Enter the objectives of the lesson plan. Max. 255 characters'),
            ])
            ->statePath('createData')
            ->model(LessonPlan::class);
    }

    public function create(): void
    {
        $createData = $this->form->getState();

        $activeSession = session()->get('currentSession');
        $activeTerm = session()->get('currentTerm');

        $createData['files'] = $createData['attachments'];

        unset($createData['attachments']);
        $createData['session_id'] = $activeSession->id;
        $createData['term_id'] = $activeTerm->id;
        $createData['teacher_id'] = auth()->id();
        $createData['status'] = LessonPlan::AWAITING_REVIEW;
        $topic = $createData['topics'];
        unset($createData['topics']);

        $createData['week_id'] = $this->week;

        $record = LessonPlan::create($createData);

        $topic = $record->topics()->attach($topic);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Lesson plan created successfully')
            ->body('Your lesson plan has been created successfully and is awaiting review.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'create-lesson-plan-modal');
        // Update the UI with the newly created lesson
        $this->dispatch('lesson-created', id: $record->id);

        // Dispatch event to notify the reviewer
        LessonPlanCreated::dispatch($record);

        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.lesson-plan.create-lesson-plan');
    }
}
