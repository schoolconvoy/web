<?php

namespace App\Filament\Resources\StudentResultResource\Pages;

use App\Filament\Resources\StudentResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentResult extends EditRecord
{
    protected static string $resource = StudentResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
