<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewStaff extends ViewRecord
{
    protected static string $view = 'filament.pages.staff.pages.view-staff';
    protected static string $resource = StaffResource::class;
}
