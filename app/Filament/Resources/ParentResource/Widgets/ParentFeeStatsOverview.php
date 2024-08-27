<?php

namespace App\Filament\Resources\ParentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ParentFeeStatsOverview extends BaseWidget
{
    public $parentId;

    protected function getStats(): array
    {
        $parent = User::find($this->parentId);
        $totalPaid = $parent->wards()->get()->sum(function ($ward) {
            return $ward->payments()->where('paid', 1)->sum('amount');
        });

        $outstandingFees = $parent->wards()->get()->sum(function ($ward) {
            return $ward->fees()->whereDoesntHave('payments')->sum('amount');
        });

        Log::debug("Total paid: " . print_r($totalPaid, true));
        Log::debug("Outstanding unpaid: " . print_r($outstandingFees, true));
        // $totalDue = parent->fees()->where('status', 'due')->sum('amount');
        return [
            Stat::make('Total paid this term', self::formatToMoney($totalPaid))
                ->description('Total amount of fees paid this term.')
                ->icon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Outstanding fees', self::formatToMoney($outstandingFees))
                ->description('Total amount of fees yet to be paid this term.')
                ->icon('heroicon-m-currency-dollar')
                ->color('danger'),
            Stat::make('Total fees', self::formatToMoney($totalPaid + $outstandingFees))
                ->description('Total amount of fees for all your wards this term.')
                ->icon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }

    public static function formatToMoney($amount)
    {
        return '₦' . number_format($amount, 2, '.', ',');
    }
}
