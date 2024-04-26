<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Harishdurga\LaravelQuiz\Models\Topic;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('name') // TODO: Change back to richtext but dropdown must be formatted
                    ->label('Question')
                    ->required(),
                FileUpload::make('media_url')
                            ->acceptedFileTypes(['image/*'])
                            ->maxFiles(1)
                            ->label('Image'),
                Select::make('topics')
                    ->label('Select a topic')
                    ->relationship('topics', 'name'),
                Toggle::make('is_active')
                        ->default(true)
                        ->label('Is active?'),
                Fieldset::make('Add Options')
                    ->schema([
                        Repeater::make('options')
                                ->relationship()
                                ->schema([
                                    RichEditor::make('name'),
                                    Toggle::make('is_correct')
                                            ->label('Correct answer?')
                                            ->onColor('success')
                                            ->offColor('danger')
                                ])
                                ->columnSpanFull()
                                ->grid(2)
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->topics()->sync($data['topics']);

        unset($data['topics']);

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
