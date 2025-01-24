<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResultResource\Pages;
use App\Filament\Resources\StudentResultResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResultResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Student Results';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationParentItem = 'Bulk Results Entry';
    protected static ?string $modelLabel = 'Student Results';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::role(User::$STUDENT_ROLE)
                    ->with('class')
            )
            ->columns([
                TextColumn::make('fullname')
                            ->searchable()
                            ->sortable(),
                TextColumn::make('class.name')
                            ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-cloud')
                    ->action(function (User $record) {
                        // Implement the download logic here
                    }),
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
            'index' => Pages\ListStudentResults::route('/'),
            // 'create' => Pages\CreateStudentResult::route('/create'),
            // 'edit' => Pages\EditStudentResult::route('/{record}/edit'),
        ];
    }
}
