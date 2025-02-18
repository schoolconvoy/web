<?php

namespace App\Shared;

use App\Filament\Resources\FeeResource\Pages;
use App\Models\Classes;
use App\Models\Fee;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;

class FeeBase extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                            ->required(),
                Select::make('fee_category')
                        ->label('Fee category')
                        ->helperText('Select a category this fee belongs to or create a new one.')
                        ->relationship('category', 'name')
                        ->createOptionForm([
                            TextInput::make('name')
                                        ->required(),
                            TextInput::make('description')
                        ])
                        ->required(),
                TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('₦'),
                Textarea::make('description')
                            ->autosize(),
                DatePicker::make('deadline'),
                Select::make('classes')
                            ->options(Classes::query()->orderBy('level_id')->get()->pluck('name', 'id'))
                            ->preload()
                            ->required(),
                Select::make('students')
                            ->relationship(
                                name: 'students',
                                modifyQueryUsing: fn ($query, $get) => $query->when(
                                    $get('classes'),
                                    fn ($query, $classId) => $query->whereHas('class', fn ($query) => $query->where('id', $classId))
                                )
                                ->orderBy('firstname')
                                ->orderBy('lastname'),
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->firstname} {$record->lastname} - {$record->class?->name}")
                            ->searchable(['firstname', 'lastname'])
                            ->helperText('Search with student name')
                            ->preload()
                            ->multiple()
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
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'view' => Pages\ViewFee::route('/{record}'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}
