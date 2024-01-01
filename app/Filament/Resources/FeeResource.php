<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Models\Fee;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use App\Shared\FeeBase;

class FeeResource extends FeeBase
{
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
        'sm' => 2,
        'lg' => 2,
    ];

    public static function table(Table $table): Table
    {
        return $table
            ->query(Fee::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')
                            ->numeric(2)
                            ->money('NGN')
                            ->summarize(
                                Summarizer::make()
                                        ->label('Total')
                                        ->using(fn (QueryBuilder $query): string => $query->sum('amount'))->money('NGN')
                            )
                            ->visible(auth()->user()->hasRole(User::$PARENT_ROLE)),
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
                                        ->visible(auth()->user()->hasRole(User::$PARENT_ROLE))
            ])
            // ->actions([
            //     Tables\Actions\Action::make('pay')
            // ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         BulkAction::make('pay')
            //                     ->requiresConfirmation()
            //                     ->action(function() {

            //                     })
            //         ,
            //     ]),
            // ])
            ;
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
