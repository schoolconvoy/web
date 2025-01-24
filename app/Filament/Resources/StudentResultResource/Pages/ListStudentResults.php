<?php

namespace App\Filament\Resources\StudentResultResource\Pages;

use App\Filament\Resources\StudentResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentResults extends ListRecords
{
    protected static string $resource = StudentResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
