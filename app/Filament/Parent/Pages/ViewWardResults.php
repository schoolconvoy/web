<?php

namespace App\Filament\Parent\Pages;

use App\Models\Result;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ViewWardResults extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.pages.view-ward-results';
    protected static ?string $navigationLabel = 'Results';
    protected static ?string $modelLabel = 'Result';
    protected static ?string $pluralModelLabel = 'Results';
    

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Result::whereHas('student', function (Builder $query) {
                    $query->whereHas('parent', function (Builder $query) {
                        $query->where('parent_id', Auth::id());
                    });
                })
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.fullname')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Class')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->sortable(),
                Tables\Columns\TextColumn::make('term.name')
                    ->label('Term')
                    ->sortable(),
                Tables\Columns\TextColumn::make('session.year')
                    ->label('Session')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ca_1')
                    ->label('CA 1')
                    ->numeric(),
                Tables\Columns\TextColumn::make('ca_2')
                    ->label('CA 2')
                    ->numeric(),
                Tables\Columns\TextColumn::make('exam_score')
                    ->label('Exam')
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total')
                    ->numeric(),
                Tables\Columns\TextColumn::make('grade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remark')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('term')
                    ->relationship('term', 'name'),
                Tables\Filters\SelectFilter::make('session')
                    ->relationship('session', 'year'),
                Tables\Filters\SelectFilter::make('class')
                    ->relationship('class', 'name'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
