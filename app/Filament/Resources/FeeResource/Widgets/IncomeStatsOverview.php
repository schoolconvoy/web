<?php

namespace App\Filament\Resources\FeeResource\Widgets;

use App\Models\Fee;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;

class IncomeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
        'sm' => 2,
        'lg' => 2,
    ];

    protected static function getTrend($query)
    {
        $data = Trend::query($query)
                ->between(
                    start: now()->subYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->sum('amount');

        return $data;
    }

    protected function getStats(): array
    {
        $paid = Payment::where('paid', 1);
        $paid = self::getTrend($paid);

        $unpaid = Fee::whereDoesntHave('payments')
                    ->where('deadline', '<=', now()->toDate());
        $unpaid = self::getTrend($unpaid);

        $student = User::role(User::$STUDENT_ROLE)->whereDoesntHave('parent');
        $student = Trend::query($student)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->count();

        return [
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
            Stat::make(
                'Total fees receieved',
                'NGN' . number_format(Payment::sum('amount'), 2, '.', ',')
            )
                ->description('Total amount of fees paid.')
                ->icon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart(
                    $paid->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
            ,
            Stat::make(
                'Unpaid fees',
                'NGN' . number_format(Fee::
                                        whereDoesntHave('payments')
                                    ->where('deadline', '<=', now()->toDate())
                                    ->sum('amount'), 2, '.', ',')
            )
                ->color('danger')
                ->description('Total amount of due payment.')
                ->icon('heroicon-m-currency-dollar')
                ->chart(
                    $unpaid->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),
        ];
    }
}
