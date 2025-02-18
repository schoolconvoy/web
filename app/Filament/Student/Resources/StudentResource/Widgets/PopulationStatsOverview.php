<?php

namespace App\Filament\Student\Resources\StudentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\DB;

class PopulationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Base query for students
        $baseQuery = User::query()->role(User::$STUDENT_ROLE);

        // Get total students count
        $totalStudents = $baseQuery->count();

        // Get gender counts using clone to prevent query modification
        $maleCount = (clone $baseQuery)->where('gender', 'Male')->count();
        $femaleCount = (clone $baseQuery)->where('gender', 'Female')->count();

        // Get students without parents count
        $withoutParentsCount = (clone $baseQuery)->whereDoesntHave('parent')->count();

        // Trend data for total students
        $totalTrend = Trend::query($baseQuery)
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Trend data for male students
        $maleTrend = Trend::query(User::query()->role(User::$STUDENT_ROLE)->where('gender', 'Male'))
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Trend data for female students
        $femaleTrend = Trend::query(User::query()->role(User::$STUDENT_ROLE)->where('gender', 'Female'))
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        // Trend data for students without parents
        $withoutParentsTrend = Trend::query(User::query()->role(User::$STUDENT_ROLE)->whereDoesntHave('parent'))
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        return [
            Stat::make('Total Students', number_format($totalStudents))
                ->description('Total number of registered students')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart(
                    $totalTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),

            Stat::make('Male Students', number_format($maleCount))
                ->description(number_format(($maleCount / max($totalStudents, 1)) * 100, 1) . '% of total students')
                ->descriptionIcon('heroicon-m-user')
                ->color('info')
                ->chart(
                    $maleTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),

            Stat::make('Female Students', number_format($femaleCount))
                ->description(number_format(($femaleCount / max($totalStudents, 1)) * 100, 1) . '% of total students')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning')
                ->chart(
                    $femaleTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),

            Stat::make('Students Without Guardian', number_format($withoutParentsCount))
                ->description('Students requiring parent assignment')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($withoutParentsCount > 0 ? 'danger' : 'success')
                ->chart(
                    $withoutParentsTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),
        ];
    }
}
