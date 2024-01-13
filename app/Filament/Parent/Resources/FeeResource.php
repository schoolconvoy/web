<?php

namespace App\Filament\Parent\Resources;

use App\Filament\Parent\Resources\FeeResource\Pages;
use App\Filament\Parent\Resources\FeeResource\Widgets\FeeStatsOverview;
use App\Models\Fee;
use App\Models\User;
use App\Shared\FeeBase;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FeeResource extends FeeBase
{
    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $ward = Cache::get('ward', 0);

                return User::find($ward)->fees()->whereDoesntHave('payments') ?? Fee::where('id', 0);
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')
                            ->numeric()
                            ->money('NGN'),
                TextColumn::make('category.name'),
            ])
            // Group summary is wrong at the moment
            ->defaultGroup(
                'category.name',
            )
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pay_with_Paystack')
                                        ->action(function () {
                                            return redirect(route('pay'));
                                        })
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
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            FeeStatsOverview::class,
        ];
    }
}
