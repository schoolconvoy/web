<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use App\Models\Fee;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title'),
                TextInput::make('percentage')
                    ->numeric(2)
                    ->suffix('%')
                    ->required()
                    ->maxValue(90),
                DatePicker::make('end_date')
                    ->helperText('Leave empty if discount has no end date'),
                Select::make('fee_id')
                    ->label('Fee')
                    ->relationship(
                        'fees',
                        'name',
                        fn ($query) => $query->with('category')->orderBy('fee_category')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                        "{$record->name} - NGN " . number_format($record->amount, 2) . " ({$record->category->name})"
                    )
                    ->required()
                    ->searchable(['name', 'category.name'])
                    ->preload()
                    ->placeholder('Select fees')
                    ->helperText('The amount shown is the full fee amount before any discounts.')
                    ,
                Select::make('student_id')
                    ->label('Student')
                    ->relationship(
                        'students',
                        'firstname',
                        fn ($query) => $query->role(User::$STUDENT_ROLE)
                            ->with('class')
                            ->orderBy('firstname')
                    )
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $className = $record->class ? $record->class->name : 'No Class';
                        return "{$record->firstname} {$record->lastname} ({$record->admission_no}) - {$className}";
                    })
                    ->required()
                    ->placeholder('Select students')
                    ->searchable(['firstname', 'lastname', 'admission_no'])
                    ->helperText('Search by name or admission number')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('percentage'),
                TextColumn::make('students.id')
                    ->formatStateUsing(fn ($state) => User::find($state)->fullname)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fees.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('View student fees')
                    ->url(
                        fn (Discount $record): string =>
                            route('filament.admin.resources.students.view', [
                                'record' => $record->students->first()->id
                            ]) . '?tab=fees'
                        )
                    ->visible(fn (Discount $record): bool => $record->students->count() > 0),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                // SoftDeletingScope::class,
            ]);
    }
}
