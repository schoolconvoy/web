<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Events\CreatedUser;
use App\Events\StudentCreatedEvent;
use App\Events\StudentIsLate;
use App\Filament\Resources\StudentResource;
use App\Models\User;
use App\Models\UserMeta;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Set;
use Filament\Forms\Components\View;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = StudentResource::class;
    public array $review = [];
    public string $password = '';

    protected function getSteps(): array
    {
        return [
            Wizard\Step::make('Bio data')
                ->icon('heroicon-s-user-circle')
                ->description('Some description')
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
                                ->maxFiles(1)
                                ->image(),
                            Radio::make('gender')
                                ->options([
                                    'Male' => 'Male',
                                    'Female' => 'Female'
                                ])
                                ->required()
                            ,
                            TextInput::make('firstname')
                                ->required(),
                            TextInput::make('lastname')
                                ->required(),
                            TextInput::make('email')
                                ->unique()
                                ->email(),
                            TextInput::make('phone')
                                ->tel()
                            ,
                            DatePicker::make('dob')
                                ->label('Date of birth')
                                ->required()
                                ->columns(),
                            TextInput::make('height')
                                ->label('Height'),
                            TextInput::make('weight')
                                ->label('Weight'),
                            Textarea::make('address')
                                ->required()
                                ->maxLength(200)
                         ])
                         ,
                ])->live(onBlur: true, debounce: 500)
                ->afterStateUpdated(function ($state) {
                    $this->review['bio'] = $state;
                }),
            Wizard\Step::make('Confirm details')
                        ->schema([
                            View::make('reviews')
                                ->view('filament.form.student-review', ['review' => $this->review])
                        ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        Log::debug('data to be saved: ' . print_r($data, true));

        try {
            $user = static::getModel()::create($data);

            // Automatically assign the student role
            $user->assignRole(User::$STUDENT_ROLE);

            $admission_no = 'ITGA-' . static::getModel()::count() + 10000;

            $user->admission_no = $admission_no;

            $password = Str::random(8);
            $this->password = $password;

            if (auth()->user()->hasAnyRole([User::$TEACHER_ROLE])) {
                $user->class_id = auth()->user()->teacher_class->id;
            }

            $data['password'] = Hash::make($this->password);
            $user->password = $data['password'];

            $user->save();

            Log::debug('Successfully saved student: ' . print_r($user, true));

        } catch (\Throwable $th) {
            Log::debug('An error has occurred when saving user ' . print_r($th, true));
            throw $th;
        }

        return $user;
    }

    protected function getCreatedNotification(): ?Notification
    {
        // show a notification including the temporary password
        return Notification::make()
            ->title('Student created successfully! Their temporary password is ' . $this->password)
            ->body('It is important that they change this password immediately to keep their account secure. Please inform them to check their email for further instructions.')
            ->persistent()
            ->success()
            ->send();
    }
}
