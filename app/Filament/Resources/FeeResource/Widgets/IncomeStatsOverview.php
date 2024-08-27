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
        // For the purpose of using the trend chart, we will keep this query simple.
        // More complete query can be seen below
        $expected_query = Fee::whereHas('students')
                        ->where('deadline', '>=', now()->toDate())
                        ->orWhere('deadline', null);

        $expected = Trend::query($expected_query)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        // Expected fees are fees that have not been paid
        // and those that have not reached their deadline or don't have one
        $expected_sum = Fee::whereHas('students')
                            ->where('deadline', '>=', now()->toDate())
                            ->orWhere('deadline', null)
                            ->get()
                            ->map(function ($fee) {
                                // Multiply the fee by the amount of students it was assigned to
                                return $fee->final_amount * $fee->students->count();
                            })
                            ->sum();

        // If any expected payment has been made, we will subtract it from the expected sum
        $paid_expected_sum = Payment::whereHas('fees', function ($query) {
                                            $query->whereHas('students')
                                                ->where('deadline', '>=', now()->toDate())
                                                ->orWhere('deadline', null);
                                        })
                                        ->sum('amount');

        $expected_sum -= $paid_expected_sum;

        $paid = Fee::whereHas('payments');
        $paid = Trend::query($paid)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        // Fees that have passed their deadline and have not been paid
        $unpaid = Fee::whereDoesntHave('payments')
                    ->where('deadline', '<=', now()->toDate());
        $unpaid = Trend::query($unpaid)
                        ->between(
                            start: now()->subYear(),
                            end: now()->endOfYear(),
                        )
                        ->perMonth()
                        ->sum('amount');

        $unpaid_sum = Fee::whereHas('students')
                        ->where('deadline', '<=', now()->toDate())
                        ->get()
                        ->map(function ($fee) {
                            // Multiply the fee by the amount of students it was assigned to
                            return $fee->final_amount * $fee->students->count();
                        })
                        ->sum();

        return [
            Stat::make(
                'Total fees receieved',
                '₦' . number_format(Payment::sum('amount'), 2, '.', ',')
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
                '₦' . number_format($unpaid_sum, 2, '.', ',')
            )
                ->color('danger')
                ->description('Total amount due as at today.')
                ->icon('heroicon-m-currency-dollar')
                ->chart(
                    $unpaid->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                ),

            Stat::make(
                'Expected fees',
                '₦' .
                number_format(
                    // TODO: Revisit when the final_amount attribute is fixed
                    $expected_sum,
                    2,
                    '.',
                    ','
                )
            )
                ->description('Total amount of fees due before the end of term.')
                ->icon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart(
                    $expected->map(fn (TrendValue $value) => $value->aggregate)->toArray()
                )
        ];
    }
}
