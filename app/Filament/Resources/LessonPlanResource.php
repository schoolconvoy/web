<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonPlanResource\Pages;
use App\Filament\Resources\LessonPlanResource\RelationManagers;
use App\Models\LessonPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class LessonPlanResource extends Resource
{
    protected static ?string $model = LessonPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessonPlans::route('/'),
            'create' => Pages\CreateLessonPlan::route('/create'),
            'view' => Pages\ViewLessonPlans::route('/{record}'),
            'edit' => Pages\EditLessonPlan::route('/{record}/edit'),
            'view-lesson' => Pages\ViewSingleLessonPlan::route('/{record}/view-plan/{plan}'),
            'approved' => Pages\ViewLessonPlans::route('/{record}/approved'),
            'pending' => Pages\ViewLessonPlans::route('/{record}/pending'),
            'mine' => Pages\ViewLessonPlans::route('/{record}/mine'),

        ];
    }
}
