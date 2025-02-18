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
use Illuminate\Support\Facades\DB;

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

    protected function getStats(): array
    {
        // Calculate total payments received (with trend)
        $paymentsData = Trend::query(
            Payment::query()
                ->where('paid', true)
        )
        ->between(
            start: now()->startOfYear(),
            end: now(),
        )
        ->perMonth()
        ->sum('amount');

        // Calculate total unpaid fees (with trend)
        $unpaidFeesQuery = Fee::query()
            ->whereHas('students')
            ->where('deadline', '<=', now())
            ->whereDoesntHave('payments');

        $unpaidData = Trend::query($unpaidFeesQuery)
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('amount');

        // Calculate expected upcoming fees (with trend)
        $expectedFeesQuery = Fee::query()
            ->whereHas('students')
            ->where(function($query) {
                $query->where('deadline', '>', now())
                    ->orWhereNull('deadline');
            })
            ->whereDoesntHave('payments');

        $expectedData = Trend::query($expectedFeesQuery)
            ->between(
                start: now()->startOfYear(),
                end: now(),
            )
            ->perMonth()
            ->sum('amount');

        // Calculate total sums
        $totalPayments = Payment::where('paid', true)->sum('amount');

        // Calculate total unpaid fees
        $totalUnpaid = Fee::whereHas('students')
            ->where('deadline', '<=', now())
            ->whereDoesntHave('payments')
            ->get()
            ->sum(function ($fee) {
                return $fee->getTotal($fee->discount_percentage) * $fee->students->count();
            });

        // Calculate total expected fees
        $totalExpected = Fee::whereHas('students')
            ->where(function($query) {
                $query->where('deadline', '>', now())
                    ->orWhereNull('deadline');
            })
            ->whereDoesntHave('payments')
            ->get()
            ->sum(function ($fee) {
                return $fee->getTotal($fee->discount_percentage) * $fee->students->count();
            });

        return [
            Stat::make('Total Fees Received', '₦' . number_format($totalPayments, 2))
                ->description('Total amount of successful payments')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($paymentsData->map(fn (TrendValue $value) => $value->aggregate ?? 0)->toArray()),

            Stat::make('Overdue Fees', '₦' . number_format($totalUnpaid, 2))
                ->description('Total amount of unpaid fees past deadline')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->chart($unpaidData->map(fn (TrendValue $value) => $value->aggregate ?? 0)->toArray()),

            Stat::make('Expected Fees', '₦' . number_format($totalExpected, 2))
                ->description('Upcoming fees not yet due')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart($expectedData->map(fn (TrendValue $value) => $value->aggregate ?? 0)->toArray()),
        ];
    }
}
