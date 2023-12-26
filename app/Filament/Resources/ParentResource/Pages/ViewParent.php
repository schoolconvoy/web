<?php

namespace App\Filament\Resources\ParentResource\Pages;

use App\Filament\Resources\ParentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParent extends ViewRecord
{
    protected static string $resource = ParentResource::class;
    protected static string $view = 'filament.pages.parent.pages.view-parent';

    protected function getHeaderActions(): array
    {
        return [
            // TODO: Fix edit page for parent
            // Actions\EditAction::make(),
        ];
    }
}
