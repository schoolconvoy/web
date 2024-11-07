<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\CBTResource\Pages\ListCBTS;
use App\Filament\Student\Resources\CBTResource\Pages\ViewCBT;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Harishdurga\LaravelQuiz\Models\Quiz;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use App\Filament\Student\Resources\CBTResource\Pages\ViewAttempt;
use App\Filament\Student\Resources\CBTResource\Pages\ViewRevision;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Support\Facades\Log;

class CBTResource extends Resource
{
    protected static ?string $model = Quiz::class;
    protected static ?string $navigationLabel = 'CBT';

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Only get quizzes assigned to this class.
                $class_id = auth()->user()->class?->id;
                $quizzes = Quiz::whereHas('quizAuthors', function (Builder $query) use ($class_id) {
                    $query->where('classes_id', $class_id);
                });

                return $quizzes;
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('pass_marks')
                            ->label('Pass mark'),
                TextColumn::make('total_marks'),
                TextColumn::make('max_attempts'),
                TextColumn::make('questions_count')
                            ->counts('questions')
                            ->label('Questions'),
            ])
            ->filters([
                Filter::make('upcoming')
                        ->query(fn (Builder $query): Builder => $query->where('valid_from', '=', now())),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                    TextEntry::make('name')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('valid_from')
                        ->dateTime('l, jS F, Y')
                        ->label('Starts from')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('valid_upto')
                        ->dateTime('l, jS F, Y')
                        ->label('Ends on')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('duration')
                        ->time('H:i:s')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('max_attempts')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('time_between_attempts')
                        ->time('H:i:s')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('pass_marks')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('total_marks')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->weight(FontWeight::Bold),
                    Actions::make([
                        Action::make('start')
                            ->icon('heroicon-m-play-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action( function () use ($infolist) {
                                    return redirect()->route('filament.student.resources.c-b-t-s.attempt', [
                                        'record' => $infolist->getState()->slug
                                    ]);
                                }
                            ),
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
            'index' => ListCBTS::route('/'),
            'view' => ViewCBT::route('/{record}'),
            'attempt' => ViewAttempt::route('/{record}/attempt'),
            'revision' => ViewRevision::route('/{record}/revision'),
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
