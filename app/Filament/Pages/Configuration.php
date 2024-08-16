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

class Configuration extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.configuration';

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog';
}
