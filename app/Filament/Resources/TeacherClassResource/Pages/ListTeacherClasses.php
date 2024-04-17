<?php

namespace App\Filament\Resources\TeacherClassResource\Pages;

use App\Filament\Resources\TeacherClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ClassResource;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListTeacherClasses extends ListRecords
{
    protected static string $resource = TeacherClassResource::class;
    protected static string $view = 'filament.pages.classes.pages.view-class';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::role(User::$STUDENT_ROLE))
            ->columns([
                TextColumn::make('name')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
