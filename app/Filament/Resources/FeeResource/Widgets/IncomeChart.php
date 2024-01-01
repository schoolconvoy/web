<?php

namespace App\Filament\Resources\FeeResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Fee;
use App\Models\Payment;

class IncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Income chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Trend::model(Payment::class)
                        ->between(
                            start: now()->startOfMonth(),
                            end: now()->endOfMonth(),
                        )
                        ->perDay()
                        ->sum('amount');
        return [
            'datasets' => [
                [
                    'label' => 'Payments receieved',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
