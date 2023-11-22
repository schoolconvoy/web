<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;

class ParentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Parents';
    protected static ?string $modelLabel = 'Parents';
    public const PARENT_STUDENT_RELATIONSHIP = 'parent_student';

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
            ->query(User::role(User::$PARENT_ROLE))
            ->columns([
                TextColumn::make('firstname'),
                TextColumn::make('lastname'),
                TextColumn::make('phone'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListParents::route('/'),
            'create' => Pages\CreateParent::route('/create'),
            'view' => Pages\ViewParent::route('/{record}'),
            'edit' => Pages\EditParent::route('/{record}/edit'),
        ];
    }
}
