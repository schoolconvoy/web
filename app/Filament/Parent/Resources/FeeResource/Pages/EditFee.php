<?php

namespace App\Filament\Parent\Resources\FeeResource\Pages;

use App\Filament\Parent\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFee extends EditRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
