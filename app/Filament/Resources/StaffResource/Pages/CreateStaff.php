<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                FileUpload::make('picture')
                    ->imagePreviewHeight('2')
                    ->loadingIndicatorPosition('left')
                    ->panelAspectRatio('16:2')
                    ->panelLayout('integrated')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left')
                    ->columnSpanFull(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
                DatePicker::make('dob')
                    ->label('Date of birth'),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Select::make('roles')
                    ->label('Assign role')
                    ->relationship('roles', 'name', function ($query) {
                        return $query->where('name', '!=', 'super-admin');
                    })
                ,
                TextInput::make('address')
                    ->required()
            ]);
    }


    protected function handleRecordCreation(array $data): Model
    {
        $role = $data['roles'];

        unset($data['roles']);

        $data['password'] = Hash::make('password');

        $user = static::getModel()::create($data);

        $email = $user->email;

        // TODO Need to use Filament's existing password reset logic
        // $status = Password::sendResetLink(array('email' => $email), function() {
        //
        // });
        //
        // $sentLink = $status === Password::RESET_LINK_SENT ? "Success" : "Error";

        // Log::debug('Role = ' . print_r($role, true) . ' User = ' . print_r($user, true));

        return $user;
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
