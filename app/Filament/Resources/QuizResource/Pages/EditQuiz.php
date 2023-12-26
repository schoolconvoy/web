<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Classes;
use App\Models\QuizClasses;
use Filament\Resources\Pages\CreateRecord;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;


class EditQuiz extends EditRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSteps(): array
    {
        return [
            Wizard\Step::make('Quiz information')
            ->icon('heroicon-o-document-plus')
            ->description('Create a new quiz for students')
            ->schema([
                Section::make('Quiz')
                    ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        Grid::make([
                            'sm' => 2,
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->schema([
                            TextInput::make('name')
                                ->label('What is this quiz for?')
                                ->helperText('e.g: Grade 1 Math')
                                ->required(),
                            Textarea::make('description')
                                ->label('Write a short description about this quiz')
                                ->helperText('e.g: Test your knowledge of Algebra'),
                            DatePicker::make('valid_from')
                                ->label('Start')
                                ->helperText('When does this quiz start?')
                                ->required()
                                ->default(now()),
                            DatePicker::make('valid_upto')
                                ->label('Deadline')
                                ->helperText('When does this quiz end?')
                                ->default(now()),
                            TextInput::make('pass_marks')
                                ->label('Pass mark')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText('Minimum mark required to pass'),
                            TextInput::make('total_marks')
                                ->label('Highest mark')
                                ->numeric()
                                ->minValue(1)
                                ->required()
                                ->helperText('Maximum mark for this quiz'),
                        ])
                ]),
                Section::make('Attempts')
                        ->description('Configure how the quiz should taken')
                        ->schema([
                            Grid::make([
                                'sm' => 2,
                                'xl' => 2,
                                '2xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('max_attempts')
                                    ->helperText('Maximum number of times the quiz can be attempted')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->suffix(''),
                                TextInput::make('time_between_attempts')
                                    ->helperText('Time in seconds between each attempts')
                                    ->required()
                                    ->suffix('seconds'),
                                TextInput::make('duration')
                                    ->helperText('How long should each attempt last for?')
                                    ->required()
                                    ->suffix('seconds'),
                            ])
                        ]),
                Section::make('Class')
                    ->description('Assign this quiz to a class')
                    ->schema([
                        Grid::make([
                            'sm' => 1,
                            'xl' => 1,
                            '2xl' => 1,
                        ])
                        ->schema([
                            Select::make('classes')
                                ->label('Class')
                                ->searchable()
                                ->options(Classes::all(['name', 'id'])->pluck('name', 'id'))
                        ])
                    ])
            ]),
            Wizard\Step::make('Add Questions')
                ->icon('heroicon-s-user-circle')
                ->description('Set the questions for this quiz')
                ->schema([
                    Repeater::make('quiz')
                            ->label('Questions')
                            ->relationship('questions')
                            ->schema([
                                // Select::make('topics')
                                //         ->label('Select a topic (optional)'),
                                // TODO: Editing topics is not working atm
                                Select::make('question_id')
                                        ->label('Select a question')
                                        ->allowHtml()
                                        ->preload()
                                        ->searchable()
                                        ->relationship('question', 'name'),
                                TextInput::make('marks')
                                        ->numeric()
                                        ->helperText('How many marks does this question carry?')
                                        ->default(1),
                            ])
                ])
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // TODO: Handle classes
        unset($data['classes']);

        $record->update($data);

        return $record;
    }
}
