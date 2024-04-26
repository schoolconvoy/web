<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassResource\Pages;
use App\Filament\Resources\ClassResource\RelationManagers;
use App\Models\Classes;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Support\Enums\FontWeight;

class ClassResource extends Resource
{
    protected static ?string $model = Classes::class;

    // protected static ?string $navigationLabel = 'Class';
    // protected static ?string $modelLabel = 'Class';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

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
            ->query(function () {
                if (auth()->user()->hasRole(User::$SUPER_ADMIN_ROLE)) {
                    return Classes::query();
                } else {
                    return auth()->user()->isHighSchool() ? Classes::highSchool() : Classes::elementarySchool();
                }
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('level.shortname')
                            ->label('Shortname'),
                TextColumn::make('users_count')
                            ->label('Students')
                            ->counts('users'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClasses::route('/'),
            'create' => Pages\CreateClass::route('/create'),
            'view' => Pages\ViewClasses::route('/{record}'),
            'edit' => Pages\EditClass::route('/{record}/edit'),
            'my-class' => Pages\MyClass::route('/{record}/my-class'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
