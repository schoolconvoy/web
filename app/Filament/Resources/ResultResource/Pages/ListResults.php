<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use App\Filament\Resources\ResultResource\Widgets\AveragePassesBar;
use App\Filament\Resources\ResultResource\Widgets\AveragePassesChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            AveragePassesChart::make(),
            AveragePassesBar::make(),
        ];
    }
}
