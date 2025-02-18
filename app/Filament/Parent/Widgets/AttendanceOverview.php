<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Attendance;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class AttendanceOverview extends BaseWidget
{
    protected static function getTrend($status)
    {
        return Trend::query(
            Attendance::query()
                ->where('student_id', Cache::get('ward'))
                ->where('status', $status)
        )
        ->between(
            start: now()->startOfYear(),
            end: now(),
        )
        ->perMonth()
        ->count();
    }

    protected function getStats(): array
    {
        $wardId = Cache::get('ward');

        // Get counts for each status
        $presentCount = Attendance::where('student_id', $wardId)
            ->where('status', Attendance::PRESENT)
            ->count();

        $lateCount = Attendance::where('student_id', $wardId)
            ->where('status', Attendance::LATE)
            ->count();

        $absentCount = Attendance::where('student_id', $wardId)
            ->where('status', Attendance::ABSENT)
            ->count();

        // Get trends
        $presentTrend = self::getTrend(Attendance::PRESENT);
        $lateTrend = self::getTrend(Attendance::LATE);
        $absentTrend = self::getTrend(Attendance::ABSENT);

        return [
            Stat::make('Present', number_format($presentCount))
                ->description('Days present in school')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart(
                    $presentTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->color('success'),

            Stat::make('Late', number_format($lateCount))
                ->description('Days arrived late')
                ->descriptionIcon('heroicon-m-clock')
                ->chart(
                    $lateTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->color('warning'),

            Stat::make('Absent', number_format($absentCount))
                ->description('Days absent from school')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart(
                    $absentTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->color('danger'),
        ];
    }
}
