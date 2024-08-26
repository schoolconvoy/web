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

        Log::debug("Total paid: " . print_r($totalPaid, true));
        // $totalDue = parent->fees()->where('status', 'due')->sum('amount');
        return [
            Stat::make('Total paid this term', '₦' . number_format($totalPaid, 2, '.', ','))
                ->description('Total amount of fees paid this term.')
                ->icon('heroicon-m-currency-dollar')
                ->color('primary'),
            // Stat::make('Bounce rate', '21%'),
            // Stat::make('Average time on page', '3:12'),
        ];
    }
}
