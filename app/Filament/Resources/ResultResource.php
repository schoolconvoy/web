<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResultResource\Pages;
use App\Filament\Resources\ResultResource\RelationManagers;
use App\Filament\Resources\ResultResource\Widgets\AveragePassesBar;
use App\Filament\Resources\ResultResource\Widgets\AveragePassesChart;
use Filament\Forms;
use App\Models\Result;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Session;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'xs' => 1,
                    'sm' => 2,
                    'lg' => 3,
                ])
                ->schema([
                    TextInput::make('ca_1')
                        ->label('CA 1')
                        ->numeric()
                        ->helperText('CA 1 and CA 2 are the continuous assessment scores')
                        ->required(),
                    TextInput::make('ca_2')
                        ->numeric()
                        ->label('CA 2')
                        ->helperText('CA 1 and CA 2 are the continuous assessment scores')
                        ->required(),
                    TextInput::make('exam_score')
                        ->numeric()
                        ->label('Exam Score')
                        ->helperText('Exam score is the score obtained in the final examination')
                        ->required(),
                    TextInput::make('total_score')
                        ->numeric()
                        ->label('Total Score')
                        ->helperText('Total score is the sum of CA 1, CA 2 and Exam Score')
                        ->required(),
                    Select::make('grade')
                        ->label('Grade')
                        ->searchable()
                        ->placeholder('Select a grade')
                        ->helperText('Select a grade from the options provided')
                        ->options([
                            'A' => 'A (Above 75%)',
                            'B' => 'B (70%)',
                            'C' => 'C (65%)',
                            'D' => 'D (60%)',
                            'E' => 'E (55%)',
                            'F' => 'F (Below 50%)',
                        ])
                        ->required(),
                    TextInput::make('remark')
                        ->label('Remark')
                        ->helperText('Remark is the comment on the student\'s performance')
                        ->required(),
                    Select::make('student_id')
                        ->label('Student')
                        ->searchable()
                        ->options(User::studentsDropdown())
                        ->required(),
                    Select::make('subject_id')
                        ->label('Subject')
                        ->searchable()
                        ->options(Subject::pluck('name', 'id'))
                        ->required(),
                    Select::make('class_id')
                        ->label('Class')
                        ->searchable()
                        ->options(function () {
                            if (auth()->user()->isHighschool()) {
                                return Classes::whereIn('name', User::$HIGH_SCHOOL_CLASSES)->pluck('name', 'id');
                            }

                            return Classes::whereIn('name', User::$ELEMENTARY_SCHOOL_CLASSES)->pluck('name', 'id');
                        })
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Result::groupBy('term_id', 'session_id', 'class_id', 'subject_id'))
            ->columns([
                TextColumn::make('term.name'),
                TextColumn::make('session.year'),
                TextColumn::make('class.name')
                    ->formatStateUsing(function ($record) {
                        return "{$record->class->name} ({$record?->class->level?->shortname})";
                    })
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->formatStateUsing(function ($record) {
                        return "{$record->subject->name} ({$record?->subject->code})";
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('session_id')
                    ->options(function () {
                        return Session::pluck('year', 'id');
                    })
                    ->label('Session'),
                Tables\Filters\SelectFilter::make('class_id')
                    ->options(function () {
                        return Classes::pluck('name', 'id');
                    })
                    ->label('Class'),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->options(function () {
                        return Subject::pluck('name', 'id');
                    })
                    ->label('Subject'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListResults::route('/'),
            'create' => Pages\CreateResult::route('/create'),
            'edit' => Pages\EditResult::route('/{record}/edit'),
            'view' => Pages\ViewResult::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AveragePassesChart::class,
            AveragePassesBar::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
