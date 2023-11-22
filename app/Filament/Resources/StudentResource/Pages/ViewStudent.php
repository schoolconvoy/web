<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.pages.student.pages.view-student';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
