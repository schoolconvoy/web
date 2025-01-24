<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
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
                Fee::query()
                    ->select('fees.*', 'classes.name as class_name', 'classes.id as class_id')
                    ->join('fee_student', 'fee_student.fee_id', '=', 'fees.id')
                    ->join('users', 'fee_student.student_id', '=', 'users.id')
                    ->join('classes', 'users.class_id', '=', 'classes.id')
                    ->groupBy('fees.id')
                    ->orderBy('classes.level_id', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                        ->searchable(),
                TextColumn::make('amount')
                            ->numeric(2)
                            ->money('NGN'),
                TextColumn::make('class_name'),
                TextColumn::make('students_count')
                                ->counts('students')
                                ->label('Students'),

            ])
            ->defaultGroup(
                'class_name'
            )
            ->filters([
                SelectFilter::make('class_id')
							->options(
								fn () => \App\Models\Level::all()->pluck('name', 'id')
							)
							->label('Level')
            ])
            ->headerActions([
                Tables\Actions\Action::make('pay_with_Paystack')
                                        ->action(function () {
                                            return redirect(route('pay'));
                                        })
                                        ->visible(auth()->user()->hasRole(User::$PARENT_ROLE))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
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
