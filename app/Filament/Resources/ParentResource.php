<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

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
                TextColumn::make('title')
                            ->sortable(),
                TextColumn::make('firstname'),
                TextColumn::make('lastname')
                            ->sortable()
                            ->searchable(),
                TextColumn::make('wards.class.name')->label('Ward class(es)'),
                TextColumn::make('wards_count')
                            ->sortable()
                            ->counts('wards')
                            ->label('Wards'),
                TextColumn::make('phone'),
                TextColumn::make('email'),
            ])
            ->filters([
                SelectFilter::make('class_filter')
                    ->relationship('wards.class', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('wards')
                    ->label('Parents with ward(s)')
                    ->nullable()
                    ->placeholder('All parents')
                    ->trueLabel('With ward(s)')
                    ->falseLabel('Without ward(s)')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('wards'),
                        false: fn (Builder $query) => $query->whereDoesntHave('wards'),
                        blank: fn (Builder $query) => $query,
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('firstname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('lastname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('email')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('dob')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('address')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
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

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$HIGH_PRINCIPAL_ROLE,
            User::$ELEM_PRINCIPAL_ROLE,
            User::$SUPER_ADMIN_ROLE
        ]);
    }
}
