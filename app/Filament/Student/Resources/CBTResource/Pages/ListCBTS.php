<?php

namespace App\Filament\Student\Resources\CBTResource\Pages;

use App\Filament\Student\Resources\CBTResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCBTS extends ListRecords
{
    protected static string $resource = CBTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
