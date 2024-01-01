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
        return [
            Stat::make(
                    'Total fees paid',
                    'NGN' . number_format(User::find(Cache::get('ward'))->class->fees()->whereHas('payments')->sum('amount'), 2, '.', ',')
                )
                ->description('Total amount of fees.')
                ->icon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make(
                    'Fees pending',
                    'NGN' . number_format(User::find(Cache::get('ward'))->class->fees()->whereDoesntHave('payments')->sum('amount'), 2, '.', ',')
                )
                ->description('Total amount of fees.')
                ->icon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make(
                    'Fees overdue',
                    'NGN' . number_format(User::find(Cache::get('ward'))->class->fees()->whereDate('deadline', '<=', now()->toDate())->sum('amount'), 2, '.', ',')
                )
                ->description('Total amount of fees.')
                ->icon('heroicon-m-currency-dollar')
                ->chart(
                    User::find(Cache::get('ward'))->class->fees()->whereDate('deadline', '<=', now()->toDate())->pluck('amount')->toArray()
                )
                ->color('primary'),
        ];
    }
}
