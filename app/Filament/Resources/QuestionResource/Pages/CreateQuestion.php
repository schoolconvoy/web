<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuizResource;
use App\Models\Classes;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Harishdurga\LaravelQuiz\Models\Question;
use Harishdurga\LaravelQuiz\Models\QuestionOption;
use Harishdurga\LaravelQuiz\Models\QuizQuestion;
use Harishdurga\LaravelQuiz\Models\Topic;
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Ramsey\Uuid\Guid\Fields;

class CreateQuestion extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = QuestionResource::class;

    protected function getSteps(): array
    {
        return [
            Wizard\Step::make('Create Questions')
            ->icon('heroicon-o-document-plus')
            ->description('Add questions to the question bank')
            ->schema([
                Repeater::make('questions')
                        ->schema([
                            RichEditor::make('name') // TODO: Change back to richtext but dropdown must be formatted
                                ->label('Question')
                                ->required(),
                            FileUpload::make('media_url')
                                ->acceptedFileTypes(['image/*'])
                                ->maxFiles(1)
                                ->label('Image'),
                            Select::make('topics')
                                ->relationship('topics', 'name')
                                ->label('Select a topic')
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->live(true)
                                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                                return;
                                            }

                                            $set('slug', Str::slug($state));
                                        })
                                        ->required(),
                                    TextInput::make('slug')
                                        ->required()
                                ])
                                ->searchable(),
                            Toggle::make('is_active')
                                    ->default(true)
                                    ->label('Is active?'),
                            Section::make('Add options')
                                    ->description('You may add as many options as possible, but we recommend adding a maximum of 4 options.')
                                    ->schema([
                                        Repeater::make('options')
                                                ->relationship('options')
                                                ->schema([
                                                    RichEditor::make('name')
                                                                ->disableToolbarButtons(['uploadFiles']),
                                                    Toggle::make('is_correct')
                                                            ->label('Correct answer?')
                                                            ->onColor('success')
                                                            ->offColor('danger')
                                                ])
                                                ->collapsible()
                                                ->columnSpanFull()
                                                ->grid(2)
                                    ])
                                    ->collapsible()
                        ])
            ])
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        foreach($data['questions'] as $question)
        {
            $topic = $question['topics'];

            unset($question['topics']);

            $model = static::getModel()::create($question);

            $topic = $model->topics()->attach($topic);
        }

        return static::getModel()::latest()->first();
    }
}
