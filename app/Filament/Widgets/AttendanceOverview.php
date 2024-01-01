<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AttendanceOverview extends ChartWidget
{
    protected static ?string $heading = 'Attendance Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Trend::model(Attendance::class)
                        ->between(
                            start: now()->startOfYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Attendance',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
