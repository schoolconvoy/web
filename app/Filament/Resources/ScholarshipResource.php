<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipResource\Pages;
use App\Models\Scholarship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('student_id')
                    ->label('Student')
                    ->relationship(
                        'student',
                        'firstname',
                        fn ($query) => $query->role(User::$STUDENT_ROLE)
                            ->with('class')
                            ->orderBy('firstname')
                    )
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $className = $record->class ? $record->class->name : 'No Class';
                        return "{$record->firstname} {$record->lastname} ({$record->admission_no}) - {$className}";
                    })
                    ->searchable(['firstname', 'lastname', 'admission_no'])
                    ->preload()
                    ->required()
                    ->helperText('Search by name or admission number'),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('₦')
                    ->maxValue(9999999.99),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->helperText('Leave empty if scholarship has no end date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.firstname')
                    ->label('Student')
                    ->formatStateUsing(fn (Model $record): string => "{$record->student->firstname} {$record->student->lastname}")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('View student fees')
                    ->url(fn (Scholarship $record): string => route('filament.admin.resources.students.view', ['record' => $record->student_id]) . '?tab=fees'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'edit' => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->fullname;
        return $data;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            User::$ADMIN_ROLE,
            User::$SUPER_ADMIN_ROLE,
            User::$ACCOUNTANT_ROLE
        ]);
    }
}
