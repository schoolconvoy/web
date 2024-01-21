<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use App\Filament\Resources\FeeResource\Widgets\IncomeStatsOverview;
use App\Filament\Resources\FeeResource\Widgets\IncomeChart;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeStatsOverview::class,
            IncomeChart::class
        ];
    }
}
