<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use App\Models\Classes;
use App\Models\QuizClasses;
use Filament\Actions;
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


class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;
    use CreateRecord\Concerns\HasWizard;

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
                                    ->suffix('minutes'),
                                TextInput::make('duration')
                                    ->helperText('How long should each attempt last for?')
                                    ->required()
                                    ->suffix('minutes'),
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
                                ->required()
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
                            ->schema([
                                // TODO: In an ideal world, we want them to be able to filter the questions by topic but
                                // TODO: the questions aren't populated based on selected topic at the moment
                                // Select::make('topics')
                                //         ->searchable()
                                //         ->label('Select a topic (optional)')
                                //         ->createOptionForm([
                                //             TextInput::make('name')->required()
                                //         ])
                                //         ->model(Topic::class)
                                //         ->createOptionUsing(function ($data) {
                                //             Topic::create([
                                //                 'name' => $data['name'],
                                //                 'slug' => Str::slug($data['name']),
                                //             ]);
                                //         })
                                //         ->options(Topic::all(['name', 'id'])->pluck('name', 'id'))
                                //         ->live(true),
                                Select::make('questions')
                                        ->relationship('questions.question', 'name')
                                        ->preload()
                                        ->allowHtml()
                                        ->searchable()
                                        ->createOptionForm([
                                            Textarea::make('name') // TODO: Change back to richtext but dropdown must be formatted
                                                ->label('Question')
                                                ->required(),
                                            Select::make('question_type_id')
                                                ->label('Question type')
                                                ->default(1)
                                                ->options([
                                                    1 => 'multiple_choice_single_answer',
                                                    2 => 'multiple_choice_multiple_answer',
                                                    3 => 'fill_the_blank'
                                                ])
                                                ->default(1),
                                            Toggle::make('is_active')
                                                    ->default(true)
                                                    ->label('Is active?'),
                                            Fieldset::make('Options')
                                                ->schema([
                                                    Repeater::make('options')
                                                            ->schema([
                                                                TextArea::make('answer'),
                                                                Toggle::make('is_correct')
                                                                        ->label('Correct answer?')
                                                                        ->onColor('success')
                                                                        ->offColor('danger')
                                                            ])
                                                ])
                                        ])
                                        ->createOptionUsing(function($data) {
                                            $question = Question::create([
                                                'name' => $data['name'],
                                                'question_type_id' => $data['question_type_id'],
                                                'is_active' => $data['is_active']
                                            ]);

                                            $options = $data['options'];

                                            foreach($options as $option)
                                            {
                                                QuestionOption::create([
                                                    'question_id' => $question->id,
                                                    'name' => $option['answer'],
                                                    'is_correct' => (bool) $option['is_correct'],
                                                ]);
                                            }

                                            Log::debug('Data from modal '. print_r([$data], true));
                                        }),
                                TextInput::make('marks')
                                            ->numeric()
                                            ->helperText('How many marks does this question carry?')
                                            ->default(1),
                            ])
                ])
        ];
    }

    /**
     * TODO: Leaving out topic for now, will revisit. Consult TODO comment
     * TODO: above
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Convert to seconds
        $data['duration'] = $data['duration'] * 60;
        $data['time_between_attempts'] = $data['time_between_attempts'] * 60;

        $questions_array = $data['quiz'];
        $classes = $data['classes'];

        $data['slug'] = Str::slug($data['name']) . '-' . static::getModel()::count() +  1000 . '-' . rand(1234, 9999);
        $data['is_published'] = 1;

        unset($data['classes']);
        unset($data['quiz']);

        // Create quiz
        $quiz = static::getModel()::create($data);

        // $topics = collect($questions_array)->pluck('topics');

        foreach($questions_array as $question)
        {
            $questions = $question['questions'];
            $marks = $question['marks'];
            // $topics = $question['topics'];

            // Associate question with quiz
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_id' => $questions,
                'marks' => $marks,
                'order' => 1, // TODO: (good to have): allow reordering of questions
                'negative_marks' => 0,
                'is_optional' => false // Note: Nigerian exams hardly have optional questions. Review in future
            ]);
        }

        // Associate quiz with class
        $quiz->quizAuthors()->save(
            QuizClasses::create([
                'quiz_id' => $quiz->id, // In an ideal world we won't have to specify this (if the relationship worked!)
                'classes_id' => $classes,
                'is_active' => 1,
            ])
        );

        // $quiz->topics()->attach($topics);

        return $quiz;
    }
}
