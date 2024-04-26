<?php

namespace App\Filament\Resources\ClassResource\Pages;

use App\Filament\Resources\ClassResource;
use App\Models\Classes;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MyClass extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ClassResource::class;

    protected static string $view = 'filament.pages.classes.pages.view-class';

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

    public function mount(int | string $record): void
    {
        Log::info('MyClass mount');
        $this->record = $this->resolveRecord($record);
    }
}
