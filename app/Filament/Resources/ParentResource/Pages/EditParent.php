<?php

namespace App\Filament\Resources\ParentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Events\ParentCreated;
use App\Filament\Resources\ParentResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\View;
use Filament\Forms\Set;
use Filament\Forms\Get;

class EditParent extends EditRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ParentResource::class;
    public array $review = [];
    public array $parentStudent = [];
    public $userData = [];

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getSteps(): array
    {
        return [
            Wizard\Step::make('Bio data')
            ->icon('heroicon-s-user-circle')
            ->description('Capture personal information about parent')
            ->schema([
                Grid::make([
                        'sm' => 2,
                        'xl' => 2,
                        '2xl' => 2,
                    ])
                    ->schema([
                        FileUpload::make('picture')
                            ->label('Upload a picture')
                            ->avatar()
                            ->inlineLabel()
                            ->columns()
                            ->image(),
                        Radio::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ])
                            ->required()
                        ,
                        TextInput::make('firstname')
                            ->required(),
                        TextInput::make('lastname')
                            ->required(),
                        TextInput::make('email')
                           ->required()
                            ->email(),
                        TextInput::make('phone')
                            ->required()
                            ->tel(),
                        // TODO: Add country, state, lga
                        Textarea::make('address')
                            ->required()
                            ->maxLength(200)
                     ])
                     ,
            ])
            ->live(onBlur: true)
            ->afterStateUpdated(function($operation, $state, Set $set) {
                $this->review['bio'] = $state;
            }),
            Wizard\Step::make('Link with a student')
                ->icon('heroicon-s-user-circle')
                ->description('Attach parent with a student')
                ->schema([
                    Grid::make([
                        'sm' => 2
                    ])->schema([
                            Select::make('student')
                                ->options(function() {
                                    return User::studentsDropdown();
                                })
                                ->columns(1)
                                ->searchable()
                                ->live(),
                            // TODO: Adding a new student while editting is not working!!!
                            Select::make('relationship')
                                ->options([
                                    'father' => 'Father',
                                    'mother' => 'Mother',
                                    'guardian' => 'Guardian',
                                ])
                                ->live()
                                ->suffixAction(
                                    Action::make('Add link')
                                        ->icon('heroicon-o-user-plus')
                                        ->action(function (Set $set, Get $get, $state) {
                                            $newLink = User::find($get('student'))
                                                                ->only('firstname', 'lastname', 'admission_no', 'id');

                                            $newLink = array_merge($newLink, array(
                                                    'pivot' => ['relationship' => $get('relationship')]
                                                )
                                            );

                                            array_push($this->parentStudent, $newLink);

                                            // Update review because the view doesn't get the data otherwise
                                            $this->review['student'] = $this->bioData();
                                        })
                                        ->size('lg')
                                        ->requiresConfirmation()
                                    ),
                            View::make('students')
                                ->columns(3)
                                ->view('filament.form.parent-students-edit', ['students' => $this->parentStudent]),
                        ])
                ])->live()
                ->afterStateUpdated(function($operation, $state, Set $set, Get $get) {
                    $this->review['student'] = $this->bioData();
                }),
            Wizard\Step::make('Review and Confirm')
                ->schema([
                    View::make('reviews')
                        ->view('filament.form.parent-review', ['review' => $this->bioData()])
                ]),
        ];
    }

    public function removeWard($id)
    {
        $removalCandidate = array_filter($this->parentStudent, function ($ward) use ($id) {
            if (is_array($ward)) {
                return $ward['id'] == $id;
            }
        });

        $index = array_keys($removalCandidate)[0];

        unset($this->parentStudent[$index]);

        // Update review
        $this->review['student'] = $this->parentStudent;
    }

    public function mergeData()
    {
        $data = array_merge(
            $this->parentStudent,
            collect($this->record->wards)
                ->mapWithKeys(fn ($user) => ['students' => array($user)])
                ->toArray()
        );

        return $data;
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->parentStudent = $this->record
            ->wards
            ->map(function ($user) {
                $parent = $user->pivot->only('relationship');
                $student = $user->only('firstname', 'lastname', 'id', 'admission_no');

                return array_merge($student, array('pivot' => $parent));
            })
            ->unique()
            ->toArray();
    }

    public function bioData()
    {
        return [
            'bio' => $this->record->toArray(),
            'student' => $this->parentStudent

        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        unset($data['student']);
        unset($data['relationship']);

        $record->update($data);

        try {
            foreach($this->parentStudent as $student)
            {
                $relationship = isset($student['pivot']) ? $student['pivot']['relationship'] : null;

                if ($relationship) {
                    // We can't use sync because we need to add the relationship
                    $didAttach = false;
                    if (!$this->record->wards()->where('users.id', $student['id'])->exists()) {
                        $didAttach = $this->record->wards()->attach($student['id'], ['relationship' => $relationship]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error updating parent-student relationship: ' . $e->getMessage());
        }

        return $record;
    }
}
