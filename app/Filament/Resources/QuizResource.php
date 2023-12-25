<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Filament\Resources\QuizResource\RelationManagers;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Console\View\Components\Info;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\RepeatableEntry;

class QuizResource extends Resource
{
    protected static ?string $model = \Harishdurga\LaravelQuiz\Models\Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('valid_from')
                            ->label('Starting on')
                            ->dateTime('l, jS F, Y'),
                TextColumn::make('valid_upto')
                            ->label('Not valid after')
                            ->dateTime('l, jS F, Y'),
                TextColumn::make('created_at')
                            ->label('Created')
                            ->since(),
            ])
            ->filters([
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
                Tabs::make('Tabs')
                        ->columnSpan(2)
                        ->tabs([
                            Tabs\Tab::make('Quiz')
                                ->schema([
                                    Infolists\Components\TextEntry::make('name')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('valid_from')
                                        ->dateTime('l, jS F, Y')
                                        ->label('Starts from')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('valid_upto')
                                        ->dateTime('l, jS F, Y')
                                        ->label('Ends on')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('max_attempts')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('total_marks')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    Infolists\Components\TextEntry::make('pass_marks')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold)
                                ]),
                            Tabs\Tab::make('Questions')
                                ->badge(fn (Model $record) => $record->questions()->count())
                                ->schema([
                                    // display an infolist component showing the questions belonging to the quiz in this resource
                                    // the relationship is quiz->questions->quiz_questions
                                    // the quiz_questions table has a quiz_id and a question_id
                                    RepeatableEntry::make('questions')
                                        ->label('All questions')
                                        ->schema([
                                            Infolists\Components\ImageEntry::make('question.media_url')
                                                                            ->label('Question image'),
                                            Infolists\Components\TextEntry::make('question.name')
                                                ->html()
                                                ->size(TextEntry\TextEntrySize::Large)
                                                ->weight(FontWeight::Bold),
                                            Infolists\Components\TextEntry::make('marks')
                                                ->size(TextEntry\TextEntrySize::Large)
                                                ->weight(FontWeight::Bold),
                                            Infolists\Components\TextEntry::make('question.options')
                                                ->label('Option')
                                                ->size(TextEntry\TextEntrySize::Large)
                                                ->formatStateUsing(function ($state) {
                                                    Log::debug('state is ' . print_r($state, true));
                                                    $options = explode("},", $state);
                                                    $options = array_map(function ($item) {
                                                        $item = rtrim($item, "}");
                                                        $item = json_decode($item . "}", true);

                                                        $response = "<li class='flex'>";
                                                        $response .= (string) $item['is_correct'] === "1" ? "*" : "";
                                                        $response .= $item['name'];
                                                        $response .= "</li>";

                                                        return $response;
                                                    }, $options);

                                                    return "<ul>" . implode("", $options) . "</ul>";
                                                })
                                                ->weight(FontWeight::Bold)
                                                ->separator(' | ')
                                                ->html(),
                                        ])
                                ])
                        ])
                        ->persistTabInQueryString()
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'view' => Pages\ViewQuiz::route('/{record}'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
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
