<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Filament\Forms\Components\View;

class EditStudent extends EditRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = StudentResource::class;
    public array $review = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

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
                                ->unique('users', 'email', $this->record)
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $record->update($data);

            // if (auth()->user()->hasAnyRole([User::$TEACHER_ROLE])) {
            //     $this->record->class_id = auth()->user()->teacher_class->id;
            // }

        } catch (\Throwable $th) {
            Log::debug('An error has occurred when saving user ' . print_r($th, true));
            throw $th;
        }

        return $record;
    }
}
