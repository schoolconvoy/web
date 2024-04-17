<?php

namespace App\Filament\Resources\TeacherClassResource\Pages;

use App\Filament\Resources\TeacherClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherClass extends EditRecord
{
    protected static string $resource = TeacherClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
