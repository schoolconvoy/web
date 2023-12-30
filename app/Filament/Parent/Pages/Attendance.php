<?php

namespace App\Filament\Parent\Pages;

use App\Filament\Parent\Widgets\AttendanceOverview;
use Filament\Pages\Page;
use App\Livewire\CalendarWidget;

class Attendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.parent.pages.attendance';

    protected function getHeaderWidgets(): array
    {
        return [
            AttendanceOverview::class
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }
}
