<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentFeeResource\Pages;
use App\Filament\Resources\StudentFeeResource\RelationManagers;
use App\Models\Fee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Filament\Tables\Actions\Action;

class StudentFeeResource extends Resource
{
    protected static ?string $model = Fee::class;
    protected static ?string $navigationParentItem = 'Fees';
    protected static ?string $navigationLabel = 'Student Fees';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        'users.id AS student_id',
                        'users.firstname AS firstname',
                        'users.lastname AS lastname',
                        'classes.id AS class_id',
                        'classes.name AS class_name',
                        DB::raw('SUM(fees.amount) AS total_fee'),
                        DB::raw('COUNT(DISTINCT fee_student.student_id) AS total_students')
                    )
                    ->groupBy('users.id')
            )
            ->columns([
                TextColumn::make('firstname')->searchable(),
                TextColumn::make('lastname')->searchable(),
                TextColumn::make('class_name')->searchable(),
                TextColumn::make('total_fee')
                                ->numeric(2)
                                ->money('NGN'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Fee Breakdown')
                    ->url(
                        fn (Fee $fee): string =>
                            route('filament.admin.resources.students.view', [
                                'record' => $fee->student_id
                            ]) . '?tab=fees'
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
            'index' => Pages\ListStudentFees::route('/'),
            'create' => Pages\CreateStudentFee::route('/create'),
            'view' => Pages\ViewStudentFee::route('/{record}'),
            'edit' => Pages\EditStudentFee::route('/{record}/edit'),
        ];
    }
}
