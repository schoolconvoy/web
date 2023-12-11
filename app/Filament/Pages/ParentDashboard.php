<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class ParentDashboard extends BaseDashboard
{
    // use HasFiltersAction;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.parent-dashboard';

    // protected static ?string $title = 'Parent dashboard';

    // protected static string $routePath = 'finance';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         FilterAction::make()
    //             ->form([
    //                 DatePicker::make('startDate'),
    //                 DatePicker::make('endDate'),
    //             ]),
    //     ];
    // }

}
