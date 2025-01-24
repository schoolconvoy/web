<?php

namespace App\Filament\Resources\ClassFeeResource\Pages;

use App\Filament\Resources\ClassFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassFees extends ListRecords
{
    protected static string $resource = ClassFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
