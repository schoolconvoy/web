<?php

namespace App\Filament\Student\Resources\StudentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PopulationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $student = User::role(User::$STUDENT_ROLE);
        $all = Trend::query($student)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();

        $female = Trend::query(User::role(User::$STUDENT_ROLE)->where('gender', 'female'))
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();

        $male = Trend::query(User::role(User::$STUDENT_ROLE)->where('gender', 'male'))
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();

        $student = User::role(User::$STUDENT_ROLE)->whereDoesntHave('parent');
        $student = Trend::query($student)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();
        return [
            Stat::make('Total students', User::role(User::$STUDENT_ROLE)->count())
                ->color('success')
                ->icon('heroicon-m-users')
                ->chart(
                    $all->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->description('Total amount of students registered.'),
            Stat::make('Total male students', User::role(User::$STUDENT_ROLE)->where('gender', 'male')->count())
                ->color('success')
                ->icon('heroicon-m-users')
                ->chart(
                    $female->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->description('Total amount of students that are male.'),
            Stat::make('Total female students', User::role(User::$STUDENT_ROLE)->where('gender', 'female')->count())
                ->color('success')
                ->icon('heroicon-m-users')
                ->chart(
                    $male->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
                ->description('Total amount of students that are female.'),

            Stat::make(
                'Students without a guardian',
                User::role(User::$STUDENT_ROLE)->whereDoesntHave('parent')->count()
            )
                ->color('warning')
                ->description('This affects how their fees are paid.')
                ->icon('heroicon-m-users')
                ->chart(
                    $student->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),
        ];
    }
}
