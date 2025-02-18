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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FeeResource extends FeeBase
{
    public static function table(Table $table): Table
    {
        $ward = User::find(Cache::get('ward'));

        return $table
            ->query(function () use ($ward) {
                return $ward->fees()
                    ->whereDoesntHave('payments')
                    ->whereDoesntHave('waivers', function ($query) {
                        $query->where(function($q) {
                            $q->whereNull('end_date')
                                ->orWhere('end_date', '>=', now());
                        });
                    });
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->money('NGN')
                    ->description(fn (Fee $record) =>
                        $record->getTotal($record->discount_percentage) !== $record->amount
                            ? 'Discounted: NGN ' . number_format($record->getTotal($record->discount_percentage), 2)
                            : ''
                    ),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deadline')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn (Fee $record) =>
                        $record->deadline && $record->deadline <= now()
                            ? 'danger'
                            : 'success'
                    ),
            ])
            ->defaultGroup('category.name')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pay_with_Paystack')
                    ->label('Pay Now')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->visible(fn () => User::getOverallAmountWithDiscounts($ward) > 0)
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
        ];
    }

    public static function getWidgets(): array
    {
        return [
            FeeStatsOverview::class,
        ];
    }
}
