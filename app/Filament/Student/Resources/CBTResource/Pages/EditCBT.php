<?php

namespace App\Filament\Student\Resources\CBTResource\Pages;

use App\Filament\Student\Resources\CBTResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCBT extends EditRecord
{
    protected static string $resource = CBTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
