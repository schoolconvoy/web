<?php

namespace App\Filament\Parent\Resources\FeeResource\Widgets;

use App\Models\Fee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Log;

class FeeStatsOverview extends BaseWidget
{
    protected static function getTrend($query)
    {
        $data = Trend::query($query)
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
        $ward = User::find(Cache::get('ward'));

        if (!$ward) {
            return [
                Stat::make(
                    'Total fees (after discounts & waivers)',
                    'NGN 0.00'
                )
                ->description('Total amount after applying discounts, waivers and scholarships.')
                ->icon('heroicon-m-calculator')
                ->color('gray'),
                Stat::make(
                    'Amount paid',
                    'NGN 0.00'
                )
                ->description('Total amount paid so far.')
                ->icon('heroicon-m-banknotes')
                ->color('gray'),
                Stat::make(
                    'Amount pending',
                    'NGN 0.00'
                )
                ->description('Amount remaining to be paid.')
                ->icon('heroicon-m-clock')
                ->color('gray')
                ->chart([0, 0]),
            ];
        }

        $totalAmount = User::getOverallAmountWithDiscounts($ward);
        $paidAmount = $ward->payments()->sum('amount');
        $pendingAmount = max(0, $totalAmount - $paidAmount);

        return [
            Stat::make(
                    'Total fees (after discounts & waivers)',
                    'NGN ' . number_format($totalAmount, 2, '.', ',')
                )
                ->description('Total amount after applying discounts, waivers and scholarships.')
                ->icon('heroicon-m-calculator')
                ->color('success'),
            Stat::make(
                    'Amount paid',
                    'NGN ' . number_format($paidAmount, 2, '.', ',')
                )
                ->description('Total amount paid so far.')
                ->icon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make(
                    'Amount pending',
                    'NGN ' . number_format($pendingAmount, 2, '.', ',')
                )
                ->description('Amount remaining to be paid.')
                ->icon('heroicon-m-clock')
                ->color($pendingAmount > 0 ? 'warning' : 'success')
                ->chart(
                    [
                        $paidAmount,
                        $pendingAmount
                    ]
                ),
        ];
    }
}
