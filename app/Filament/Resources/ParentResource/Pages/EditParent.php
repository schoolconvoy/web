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
use App\Models\UserMeta;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Actions\Action;
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
                                            Log::debug('Parent-student relationship is ' . print_r($this->record->wards()->get()->toArray(), true));

                                            array_push($this->parentStudent, array(
                                                'student' => User::find($get('student')),
                                                'relationship' => $get('relationship'),
                                            ));

                                            // Set review here first
                                            $this->review['student'] = $this->mergeData();

                                            Log::debug('Parent-student relationship is ' . print_r($this->review['student'], true));
                                        })
                                ),
                            View::make('students')
                                ->columns(3)
                                ->view('filament.form.parent-students-edit', ['students' => $this->mergeData()]),
                        ])
                ])->live(onBlur: true)
                ->afterStateUpdated(function($operation, $state, Set $set, Get $get) {
                    $this->review['student'] = $this->mergeData();
                }),
            Wizard\Step::make('Review and Confirm')
                ->schema([
                    View::make('reviews')
                        ->view('filament.form.parent-review', ['review' => $this->review])
                ]),
        ];
    }

    public function removeWard($id)
    {
        $removalCandidate = array_filter($this->parentStudent, function ($ward) use ($id) {
            return $ward['student']['id'] == $id;
        });

        $index = array_keys($removalCandidate)[0];

        unset($this->parentStudent[$index]);

        // Update review
        $this->review['student'] = $this->parentStudent;
    }

    public function mergeData()
    {
        return array_merge(
            $this->parentStudent,
            $this->record->wards()->get()->toArray()->mapWithKeys(fn ($user) => ['student' => $user, 'relationship' => $user->pivot->relationship])
        );
    }

    public function getParentStudents()
    {
        $parent_relationships = $this->record->meta()->where('key', ParentResource::PARENT_STUDENT_RELATIONSHIP)->get()->pluck('value');

        Log::debug('parent_relationship -- ' . print_r($parent_relationships, true));

        return $parent_relationships[0] || [];
    }

}
