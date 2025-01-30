<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Actions;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Models\Fee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\Classes;
use App\Models\User;
use App\Filament\Resources\ClassResource;

class ViewClassFee extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.fee.pages.view-class-fees';

    // This will allow the record to be fetched with Classes instead of Fees.
    protected static string $resource = ClassResource::class;

    protected static string $model = Classes::class;

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function getQuery(): Builder
    {
        return $this->record->users()
            ->select(
                'users.firstname',
                'users.lastname',
                'classes.name as class_name',
                DB::raw('SUM(fees.amount) as total_fee'),
                'users.id as user_id',
                'classes.id',
                'fees.id as fee_id'
            )
            ->leftJoin('fee_student', 'users.id', '=', 'fee_student.student_id')
            ->leftJoin('fees', 'fee_student.fee_id', '=', 'fees.id')
            ->join('classes', 'users.class_id', '=', 'classes.id')
            ->groupBy('users.id', 'users.firstname', 'users.lastname', 'classes.name')
            ->getQuery();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                TextColumn::make('class_name'),
                TextColumn::make('firstname')->searchable(),
                TextColumn::make('lastname')->searchable(),
                TextColumn::make('total_fee')
                    ->numeric(2)
                    ->default(0)
                    ->money('NGN'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View fee breakdown')
                    ->url(
                        fn (User $user): string =>
                            route('filament.admin.resources.students.view', [
                                'record' => $user->user_id
                            ]) . '?tab=fees'
                        ),
            ]);
    }
}
