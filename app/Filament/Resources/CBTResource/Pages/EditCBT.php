<?php

namespace App\Filament\Resources\CBTResource\Pages;

use App\Filament\Resources\CBTResource;
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
