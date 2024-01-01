<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Attendance;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AttendanceOverview extends BaseWidget
{
    protected static function getTrend($status)
    {
        $data = Trend::query(
                    Attendance::where('status', '!=', $status)
                                ->where('student_id', Cache::get('ward'))
                )
                ->between(
                    start: now()->subYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count();

        return $data;
    }

    protected function getStats(): array
    {
        $present = self::getTrend(Attendance::PRESENT);
        $late = self::getTrend(Attendance::LATE);
        $absent = self::getTrend(Attendance::ABSENT);

        return [
            Stat::make('Arrived on time', Attendance::where('status', '!=', Attendance::PRESENT)
                                                    ->where('student_id', Cache::get('ward'))
                                                    ->count()
                    )
                    ->description('Total number of present days.')
                    ->icon('heroicon-m-star')
                    ->chart(
                        $present->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                    )
                    ->color('success')
            ,
            Stat::make(
                    'Arrived late',
                    Attendance::where('status', '!=', Attendance::LATE)
                                                        ->where('student_id', Cache::get('ward'))
                                                        ->count()
                )
                ->description('Total number of late days.')
                ->icon('heroicon-m-arrow-down')
                ->chart(
                    $late->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->color('warning'),
            Stat::make(
                    'Absent from School',
                    Attendance::where('status', '!=', Attendance::ABSENT)
                                                        ->where('student_id', Cache::get('ward'))
                                                        ->count()
            )
            ->description('Total number of absent days.')
            ->icon('heroicon-m-minus-circle')
            ->chart(
                $absent->map(fn (TrendValue $value) => $value->aggregate)->toArray()
            )
            ->color('danger'),
        ];
    }
}
