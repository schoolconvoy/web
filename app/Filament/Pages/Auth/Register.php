<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('firstname')
                    ->required()
                    ->autofocus()
                    ->maxLength(255),
                TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
                $this->getEmailFormComponent(),
                TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('school_id')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('dob')
                    ->label('Date of birth')
                    ->format('d/m/Y')
                    ->native(false)
                    ->required(),
                Select::make('gender')
                    ->required()
                    ->options(['Male', 'Female']),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent()
            ]);
    }
}
