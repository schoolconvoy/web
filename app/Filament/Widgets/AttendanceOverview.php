<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;

class AttendanceOverview extends ChartWidget
{
    protected static ?string $heading = 'Attendance Overview';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 3;

    protected function getData(): array
    {
        $presentData = Trend::query(
            Attendance::query()->where('status', Attendance::PRESENT)
        )
        ->between(
            start: now()->startOfYear(),
            end: now(),
        )
        ->perMonth()
        ->count();

        $lateData = Trend::query(
            Attendance::query()->where('status', Attendance::LATE)
        )
        ->between(
            start: now()->startOfYear(),
            end: now(),
        )
        ->perMonth()
        ->count();

        $absentData = Trend::query(
            Attendance::query()->where('status', Attendance::ABSENT)
        )
        ->between(
            start: now()->startOfYear(),
            end: now(),
        )
        ->perMonth()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Present',
                    'data' => $presentData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#22C55E', // green
                    'borderColor' => '#22C55E',
                ],
                [
                    'label' => 'Late',
                    'data' => $lateData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#F59E0B', // yellow
                    'borderColor' => '#F59E0B',
                ],
                [
                    'label' => 'Absent',
                    'data' => $absentData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#EF4444', // red
                    'borderColor' => '#EF4444',
                ],
            ],
            'labels' => $presentData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
