<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Models\Classes;
use App\Models\Fee;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Shared\FeeBase;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\Page;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FeeResource extends FeeBase
{
    protected static ?string $modelLabel = 'Fees';
    protected static ?string $navigationLabel = 'Fees';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
        'sm' => 2,
        'lg' => 2,
    ];

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Classes::query()
            )
            ->columns([
                TextColumn::make('name')->sortable(),
				TextColumn::make('level.shortname')->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Class Fee')
                    ->url(
                        fn (Classes $class): string =>
                            route('filament.admin.resources.fees.view-class', [
                                'record' => $class->id
                            ])
                        ),
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
            'view-class' => Pages\ViewClassFee::route('/class/{record}'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}
