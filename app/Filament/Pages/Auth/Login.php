<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Component;

class Login extends BaseLogin
{
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->revealable(true);
    }
}
