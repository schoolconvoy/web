<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Models\User;

class ListStaff extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = StaffResource::class;
    protected static string $view = 'filament.pages.staff.pages.list-staff';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            StaffResource\Widgets\StaffOverview::class,
        ];
    }
}
