<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Events\ParentCreated;
use App\Filament\Resources\ParentResource;
use App\Filament\Resources\StudentResource;
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
use App\Models\UserMeta;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use Filament\Forms\Get;

class CreateParent extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ParentResource::class;
    public array $review = [];
    public array $parentStudent = [];

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
            ->live()
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
                                ->required(),
                            Select::make('relationship')
                                ->options([
                                    'father' => 'Father',
                                    'mother' => 'Mother',
                                    'guardian' => 'Guardian',
                                ])
                                ->required()
                                ->suffixAction(
                                    Action::make('Add link')
                                        ->icon('heroicon-o-user-plus')
                                        ->requiresConfirmation()
                                        ->action(function (Set $set, Get $get, $state) {

                                            // TODO: Make sure unique is working. Do not repeat items already added
                                            $unique = array_filter($this->parentStudent, function($link) use ($get) {
                                                return $link['student']['id'] === $get('student');
                                            });


                                            if (count($unique) === 0) {
                                                $student = User::find($get('student'));
                                                $admission = $student->meta()->first()->getMeta(StudentResource::STUDENT_ADMISSION_NO);

                                                array_push($this->parentStudent, array(
                                                    'student' => [
                                                        'id' => $student->id,
                                                        'firstname' => $student->firstname,
                                                        'lastname' => $student->lastname,
                                                        'admission_no' => $admission,
                                                    ],
                                                    'relationship' => $get('relationship'),
                                                ));
                                            }

                                            $set('parent_relationship', $this->parentStudent);

                                            // TODO: Figure out how to safely empty the data once it's added
                                            // $set('student', '');
                                            // $set('relationship', '');

                                            Log::debug('Parent-student relationship is ' . print_r($this->parentStudent, true));
                                        })
                                ),
                            View::make('students')
                                ->columns(3)
                                ->view('filament.form.parent-students', ['students' => $this->parentStudent]),
                        ])
                ])->live()
                ->afterStateUpdated(function($operation, $state, Set $set, Get $get) {
                    Log::debug('state ==> ' . print_r($state, true) .
                                ' parentStudent ==> ' . print_r($this->parentStudent, true) .
                                ' parent-student from state ' . print_r($get('parent_relationship'))
                            );
                    $this->review['student'] = $this->parentStudent;
                }),
            Wizard\Step::make('Review and Confirm')
                ->schema([
                    View::make('reviews')
                        ->view('filament.form.review', ['review' => $this->review])
                ]),
        ];
    }

    public function infolist()
    {

    }

    public function getReviewData()
    {
        return $this->review;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $studentLink = $this->parentStudent;

        unset($data['student']);
        unset($data['relationship']);

        $user = static::getModel()::create($data);

        // Automatically assign the student role
        $user->assignRole(User::$PARENT_ROLE);

        // TODO: Allow it to be customized
        $parent_no = 'ITGA-PARENT-' . static::getModel()::count() + 10000;

        // Save meta data to user_meta
        $user->meta()->saveMany([
            new UserMeta([
                'key' => 'parent_student',
                'value' => $studentLink
            ]),
            new UserMeta([
                'key' => 'parent_no',
                'value' => $parent_no
            ]),
        ]);

        // Dispatch event
        ParentCreated::dispatch($user, $studentLink);

        return $user;
    }
}
