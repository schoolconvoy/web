<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Events\CreatedUser;
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
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateParent extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = ParentResource::class;
    public array $review = [];
    public array $parentStudent = [];
    public $userData = [];
    public string $password = '';

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
                        Select::make('title')
                            ->options([
                                'mr' => 'Mr',
                                'mrs' => 'Mrs',
                                'miss' => 'Miss',
                                'dr' => 'Dr',
                                'prof' => 'Prof',
                            ]),
                        TextInput::make('firstname')
                            ->required(),
                        TextInput::make('lastname')
                            ->required(),
                        TextInput::make('email')
                           ->required()
                           ->unique()
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
                                            // TODO: Make sure unique is working. Do not repeat items already added
                                            $unique = array_filter($this->parentStudent, function($link) use ($get) {
                                                return $link['student'] === $get('student');
                                            });

                                            Log::debug('student is ' . $get('student'));
                                            if (count($unique) === 0) {

                                                array_push($this->parentStudent, array(
                                                    'student' =>  $get('student'),
                                                    'relationship' => $get('relationship'),
                                                ));
                                            }

                                            // Set review here first
                                            // $this->review['student'] = $this->parentStudent;

                                            // TODO: Figure out how to safely empty the data once it's added
                                            // $set('student', '');
                                            // $set('relationship', '');
                                        })
                                ),
                            View::make('students')
                                ->columns(3)
                                ->view('filament.form.parent-students', ['students' => $this->parentStudent]),
                        ])
                ])->live(onBlur: true),
            Wizard\Step::make('Review and Confirm')
                ->schema([
                    View::make('reviews')
                        ->view('filament.form.parent-review', ['review' => $this->bioData()])
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

    public function mergeData()
    {
        $data = array_merge(
            $this->parentStudent,
            collect($this->record->wards ?? [])->mapWithKeys(fn ($user) => ['student' => $user])->toArray()
        );

        return $data;
    }

    public function bioData()
    {
        return [
            'bio' => $this->data ? $this->data : [],
            'student' => $this->mergeData()
        ];
    }

    public function removeWard($id)
    {
        $removalCandidate = array_filter($this->parentStudent, function ($ward) use ($id) {
            return $ward['student'] == $id;
        });

        $index = array_keys($removalCandidate)[0];

        unset($this->parentStudent[$index]);

        // Update review
        $this->review['student'] = $this->parentStudent;
    }

    protected function handleRecordCreation(array $data): Model
    {

        unset($data['student']);
        unset($data['relationship']);

        $parent = static::getModel()::create($data);

        foreach($this->parentStudent as $relationship)
        {
            $parent->wards()->attach($relationship['student'], [
                'relationship' => $relationship['relationship']
            ]);
        }

        // Automatically assign the parent role
        $parent->assignRole(User::$PARENT_ROLE);

        $parent_no = 'ITGA-PARENT-' . static::getModel()::count() + 10000;

        $parent->parent_no = $parent_no;

        $password = Str::random(8);
        $this->password = $password;

        Log::debug('Password is ' . $password);

        $data['password'] = Hash::make($this->password);
        $parent->password = $data['password'];
        $parent->save();

        // Dispatch event
        CreatedUser::dispatch($parent);

        return $parent;
    }

    protected function getCreatedNotification(): ?Notification
    {
        // show a notification including the temporary password
        return Notification::make()
            ->title('Parent created successfully! Their temporary password is ' . $this->password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();
    }
}
