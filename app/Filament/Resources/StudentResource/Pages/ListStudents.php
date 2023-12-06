<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import students')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('primary')
                ->form([
                    FileUpload::make('Import students')
                ])
                // TODO: Add role based access
                //->visible(auth()->user()->can('Create students'))
            ,
            Action::make('download')
                ->label('Download students')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    FileUpload::make('Import students')
                ]),
            CreateAction::make()
                ->model(User::class)
        ];
    }
}
