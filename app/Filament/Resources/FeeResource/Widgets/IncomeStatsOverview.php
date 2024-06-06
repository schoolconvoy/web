<?php

namespace App\Filament\Resources\FeeResource\Widgets;

use App\Models\Fee;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class IncomeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    public ?string $filter = 'today';
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
        'sm' => 2,
        'lg' => 2,
    ];

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

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
        $expected = Fee::whereHas('students')
                        ->where('deadline', '<=', now()->toDate());
        $expected = Trend::query($expected)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        $paid = Fee::whereHas('payments');
        $paid = Trend::query($paid)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        $unpaid = Fee::whereDoesntHave('payments')
                    ->where('deadline', '<=', now()->toDate());

        $unpaid = Trend::query($unpaid)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        return [
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
                                        ->get()
                                        ->sum('final_amount'), 2, '.', ',')
            )
                ->color('danger')
                ->description('Total amount of due payment.')
                ->icon('heroicon-m-currency-dollar')
                ->chart(
                    $unpaid->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),
            Stat::make(
                'Expected fees',
                'NGN' . number_format(Fee::whereHas('students')->get()->sum('final_amount'), 2, '.', ',')
            )
                ->description('Total amount of fees expected.')
                ->icon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart(
                    $paid->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
        ];
    }
}
