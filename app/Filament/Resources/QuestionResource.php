<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Tables\Filters\SelectFilter;
use Harishdurga\LaravelQuiz\Models\QuestionOption;
use Harishdurga\LaravelQuiz\Models\QuizQuestion;
use Harishdurga\LaravelQuiz\Models\Topic;
use Illuminate\Support\Facades\Log;
use Harishdurga\LaravelQuiz\Models\Question;
use Filament\Infolists;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class QuestionResource extends Resource
{
    use HasWizard;

    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-s-question-mark-circle';

    protected static ?string $navigationLabel = 'Question bank';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                            ->sortable()
                            ->html(),
                TextColumn::make('topics.name')
                            ->label('Topic'),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(auth()->user()->can('delete')),
            ])
            ->headerActions([
                // Tables\Actions\Action::make('import_questions')
                //                         ->icon('heroicon-o-document-plus')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('media_url')
                    ->url(fn ($record): string => $record->media_url ?? '')
                    ->label('Image')
                    ->columnSpanFull()
                    ->width('30%')
                    ->height('auto'),
                Infolists\Components\TextEntry::make('name')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->html(true)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('topics.name')
                    ->label('Topic')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('options')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold)
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->html(true)
                    ->formatStateUsing(
                        fn ($state): string => $state->is_correct === 1 ? $state->name . "*" : $state->name
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'view' => Pages\ViewQuestion::route('/{record}'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()->hasRole(User::$SUPER_ADMIN_ROLE |);
    // }
}
