<?php

namespace App\Filament\Pages;

use App\Models\Session;
use App\Models\Term;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Button;
use App\Models\User;
class Configuration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.configuration';

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE,
            User::$SUPER_ADMIN_ROLE
        ]);
    }
}
