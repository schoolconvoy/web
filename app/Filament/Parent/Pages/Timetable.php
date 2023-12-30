<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Timetable extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.timetable';

    protected static ?string $slug = '#';

    public static function getNavigationBadge(): ?string
    {
        return 'coming soon';
    }
}
