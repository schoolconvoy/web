<?php

namespace App\Filament\Resources\StaffResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\StaffResource\Pages\ListStaff;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Models\User;

class StaffOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListStaff::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Active staffs', $this->getPageTableQuery()->where('status', true)->count())
                ->color('success'),
            Stat::make('Inactive staffs', $this->getPageTableQuery()->where('status', false)->count())
                ->description('Remove inactive staffs')
                ->color('danger'),
            Stat::make('Teachers', User::role(User::$TEACHER_ROLE)->count())
                // ->description('Remove inactive staffs')
                ->color('danger')
        ];
    }
}
