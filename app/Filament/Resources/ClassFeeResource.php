<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassFeeResource\Pages;
use App\Models\Fee;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;

class ClassFeeResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationParentItem = 'Fees';
    protected static ?string $model = Fee::class;
    protected static ?string $navigationLabel = 'Class Fees';

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
                TextColumn::make('total_students')->sortable(),
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

    public function viewAny(User $user): bool
    {
        return true; // or some other condition
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassFees::route('/')
        ];
    }
}
