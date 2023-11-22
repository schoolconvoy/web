<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Events\StudentCreatedEvent;
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

class CreateStudent extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = StudentResource::class;

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
                         ]),
                ]),
            Wizard\Step::make('Confirm details')
                        ->schema([
                            // ...
                        ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $height = $data['height'];
        $weight = $data['weight'];

        unset($data['height']);
        unset($data['weight']);

        $user = static::getModel()::create($data);

        // Automatically assign the student role
        $user->assignRole(User::$STUDENT_ROLE);

        // TODO: Allow it to be customized
        $admission_no = 'ITGA-' . static::getModel()::count() + 10000;

        // Save meta data to user_meta
        $user->meta()->saveMany([
            new UserMeta([
                'key' => StudentResource::STUDENT_MEDICAL_RECORD,
                'value' => [
                    'height' => $height,
                    'weight' => $weight
                ]
            ]),
            new UserMeta([
                'key' => StudentResource::STUDENT_ADMISSION_NO,
                'value' => $admission_no
            ]),
        ]);

        // Dispatch event
        StudentCreatedEvent::dispatch($user);

        return $user;
    }
}
