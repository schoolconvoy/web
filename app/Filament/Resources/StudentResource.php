<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Pages\EditRecord\Concerns\HasWizard;
use Filament\Forms\Components\View;
use App\Models\Level;

class StudentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Students';
    protected static ?string $modelLabel = 'Students';

    public static array $review = [];
    public string $password = '';

    // Custom field keys
    public const STUDENT_ADMISSION_DATE = 'admission_date';
    public const STUDENT_ADMISSION_NO = 'admission_no';
    public const STUDENT_MEDICAL_RECORD = 'medical';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Bio data')
                        ->icon('heroicon-s-user-circle')
                        ->description('Basic data about the student')
                        ->schema([
                            Grid::make([
                                    'sm' => 2,
                                    'xl' => 2,
                                    '2xl' => 2,
                                ])
                                ->schema([
                                    FileUpload::make('picture')
                                        ->label('Upload a picture')
                                        ->avatar()
                                        ->inlineLabel()
                                        ->columns()
                                        ->maxFiles(1)
                                        ->image(),
                                    Radio::make('gender')
                                        ->options([
                                            'Male' => 'Male',
                                            'Female' => 'Female'
                                        ])
                                        ->required()
                                    ,
                                    TextInput::make('admission_no')
                                    ->helperText('Enter a custom admission number or click the button to generate one')
                                    ->placeholder('ITGA-10000')
                                    ->unique(ignoreRecord: true)
                                    ->suffixAction(
                                        Action::make('generateAdmissionNo')
                                            ->icon('heroicon-c-sparkles')
                                            ->action(function (Set $set, $state) {
                                                $set('admission_no', User::generateAdmissionNo());
                                            })
                                    ),
                                    TextInput::make('firstname')
                                        ->required(),
                                    TextInput::make('middle_name'),
                                    TextInput::make('lastname')
                                        ->required(),
                                    TextInput::make('email')
                                        ->unique(ignoreRecord: true)
                                        ->email(),
                                    TextInput::make('phone')
                                        ->tel(),
                                    Select::make('class_id')
                                        ->relationship('class', 'name')
                                        ->label('Assign class')
                                        ->options(
                                            User::getUserLevel()
                                        )
                                        ->nullable(),
                                    DatePicker::make('dob')
                                        ->label('Date of birth')
                                        ->required()
                                        ->columns(),
                                    TextInput::make('height')
                                        ->label('Height (cm)'),
                                    TextInput::make('weight')
                                        ->label('Weight (kg)'),
                                    ])
                                    ,
                        ])->live(onBlur: true, debounce: 500)
                        ->afterStateUpdated(function ($state) {
                            self::$review['bio'] = $state;
                        }),
                    Wizard\Step::make('More information')
                        ->icon('heroicon-s-user-circle')
                        ->description('Additional information about the student')
                        ->schema([
                            Grid::make([
                                    'sm' => 2,
                                    'xl' => 2,
                                    '2xl' => 2,
                                ])
                                ->schema([
                                    Textarea::make('address')
                                        ->required()
                                        ->maxLength(200),
                                    TextInput::make('lga')
                                        ->label('Local Government Area'),
                                    TextInput::make('state')
                                        ->label('State of origin'),
                                    Select::make('year_of_entry')
                                        ->options(
                                            array_combine(
                                                range(date('Y'), 2009), range(date('Y'), 2009)
                                            )
                                        ),
                                    Select::make('class_at_entry')
                                        ->options(Level::pluck('name', 'id')->toArray()),
                                    TextInput::make('entrance_score')
                                        ->minValue(0)
                                        ->numeric()
                                ]),
                            ])->live(onBlur: true, debounce: 100)
                            ->afterStateUpdated(function ($state) {
                                self::$review['bio'] = $state;
                            }),
                    // TODO: Make review more elegant
                    // Wizard\Step::make('Confirm details')
                    //             ->schema([
                    //                 View::make('reviews')
                    //                     ->view('filament.form.student-review', ['review' => self::$review])
                    //             ]),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        /**
         * TODO: Next we need to add classes, subjects, sessions so we can list
         * those information about a student.
         */
        return $table
            ->query(User::role(User::$STUDENT_ROLE))
            ->columns([
                TextColumn::make('admission_no')
                    ->label('Admission no.')
                    ->sortable(),
                TextColumn::make('class.name')
                    ->sortable(),
                TextColumn::make('firstname')
                    ->searchable(),
                TextColumn::make('lastname')
                    ->searchable(),
                TextColumn::make('dob')
                    ->dateTime('Y-m-d'),
                TextColumn::make('gender')
            ])
            ->filters([
                // filter by class
                SelectFilter::make('class')
                            ->relationship('class', 'name'),
                SelectFilter::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('firstname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('middle_name')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('lastname')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('email')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('phone')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('entrance_score')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('entry_class.name')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('year_of_entry')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('dob')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('address')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('lga')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('state')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('height')
                    ->label('Height (cm)')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                Infolists\Components\TextEntry::make('weight')
                    ->label('Weight (kg)')
                    ->size(TextEntry\TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
