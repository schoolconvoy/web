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
                Fee::query()
                    // Join to pivot table linking fees and students:
                    ->join('fee_student', 'fees.id', '=', 'fee_student.fee_id')
                    // Join to the students table (often "users"):
                    ->join('users', 'fee_student.student_id', '=', 'users.id')
                    // Join to the classes table:
                    ->join('classes', 'users.class_id', '=', 'classes.id')
                    ->select(
                        'fees.id',
                        'classes.id AS class_id',
                        'classes.name AS class_name',
                        DB::raw('SUM(fees.amount) AS total_fee'),
                        DB::raw('COUNT(DISTINCT fee_student.student_id) AS total_students')
                    )
                    ->groupBy('classes.id', 'classes.name')
            )
            ->columns([
                TextColumn::make('class_name')->sortable(),
                TextColumn::make('total_fee')->numeric(2)->money('NGN'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Class Fee')
                    ->url(
                        fn (Fee $fee): string =>
                            route('filament.admin.resources.fees.view-class', [
                                'record' => $fee->class_id
                            ])
                        ),
            ])
            ->filters([
                SelectFilter::make('class_id')
                    ->options(
                        User::query()
                            ->whereNotNull('class_id')
                            ->with('class')
                            ->orderBy('class_id')
                            ->get()
                            ->pluck('class.name', 'class.id')
                    )
                    ->label('Class')
                    ->default(null),
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
