<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdmissionResource\Pages;
use App\Models\User;
use App\Trait\UserTrait;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class AdmissionResource extends Resource
{
    use InteractsWithForms;
    use UserTrait;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Admission';
    protected static ?string $modelLabel = 'Admission';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Student Information')
                        ->icon('heroicon-s-user-circle')
                        ->description('Add basic Student information')
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
                                            'male' => 'Male',
                                            'female' => 'Female'
                                        ])
                                        ->required(),
                                    TextInput::make('firstname')
                                        ->required(),
                                    TextInput::make('middle_name'),
                                    TextInput::make('lastname')
                                        ->required(),
                                    TextInput::make('email')
                                        ->unique(ignoreRecord: true)
                                        ->email(),
                                    TextInput::make('phone')
                                        ->tel()->required(),
                                    DatePicker::make('dob')
                                        ->label('Date of birth')
                                        ->required()
                                        ->columns(),
                                    Select::make('class_at_entry')
                                        ->label('Assign class')
                                        ->options(
                                            UserTrait::getUserLevel()
                                        )
                                        ->nullable(),
                                    TextInput::make('height')
                                        ->label('Height')->numeric(),
                                    TextInput::make('weight')
                                        ->label('Weight')->numeric(),
                                    Textarea::make('address'),
                                ])
                        ]),

                    Step::make('Add Guardian/Parent')
                        ->icon('heroicon-s-user-circle')
                        ->description('Add Parent information')
                        ->schema([
                            Grid::make([
                                'sm' => 2,
                                'xl' => 2,
                                '2xl' => 2,
                            ])
                                ->schema([
                                    Toggle::make('existing_parent')
                                        ->onIcon('heroicon-m-bolt')
                                        ->offIcon('heroicon-m-user')
                                        ->required()
                                        ->label('An exsisting Parent')
                                        ->live()
                                        ->default(false),
                                    Section::make('Parent')
                                        ->description('Provide Email of existing Parent')
                                        ->schema([
                                            TextInput::make('parent_email')
                                                ->required()
                                                ->exists('users', 'email')
                                                ->email(),
                                            Select::make('parent_relationship')
                                                ->options([
                                                    'father' => 'Father',
                                                    'mother' => 'Mother',
                                                    'guardian' => 'Guardian',
                                                ])
                                        ])
                                        ->hidden((fn (Get $get): bool => !$get('existing_parent'))),
                                    Section::make('Add Guardian/Parent')
                                        ->description('Add Parent information')
                                        ->schema([
                                            Grid::make([
                                                'sm' => 2,
                                                'xl' => 2,
                                                '2xl' => 2,
                                            ])->schema([
                                                FileUpload::make('parent_picture')
                                                    ->label('Upload a picture')
                                                    ->avatar()
                                                    ->inlineLabel()
                                                    ->columns()
                                                    ->image(),
                                                Radio::make('parent_gender')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female'
                                                    ])
                                                    ->required(),
                                                Select::make('parent_title')
                                                    ->options([
                                                        'mr' => 'Mr',
                                                        'mrs' => 'Mrs',
                                                        'miss' => 'Miss',
                                                        'dr' => 'Dr',
                                                        'prof' => 'Prof',
                                                    ]),

                                                TextInput::make('parent_firstname')
                                                    ->required(),
                                                TextInput::make('parent_lastname')
                                                    ->required(),
                                                TextInput::make('parent_email')
                                                    ->required()
                                                    ->unique('users', 'email')
                                                    ->email(),
                                                TextInput::make('parent_phone')
                                                    ->required()
                                                    ->tel(),
                                                Select::make('parent_relationship')
                                                    ->options([
                                                        'father' => 'Father',
                                                        'mother' => 'Mother',
                                                        'guardian' => 'Guardian',
                                                    ])->required(),
                                                Textarea::make('parent_address')
                                                    ->required()
                                                    ->maxLength(200),
                                                TextInput::make('parent_lga')
                                                    ->required(),
                                                TextInput::make('parent_state')
                                                    ->required(),

                                            ])
                                        ])->hidden(fn (Get $get): bool => $get('existing_parent'))


                                ])
                        ]),
                ])
                    ->persistStepInQueryString()
                    ->submitAction(
                        new HtmlString(Blade::render(<<<BLADE
                                        <x-filament::button
                                            type="submit"
                                            size="sm"
                                        >
                                            Submit
                                        </x-filament::button>
                                    BLADE))
                    ),
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                User::role(User::$STUDENT_ROLE)->whereNull('admission_date')
            )
            ->columns([
                TextColumn::make('firstname')->label('First Name')->searchable(),
                TextColumn::make('lastname')->label('Last Name')->searchable(),
                TextColumn::make('phone')->label('Phone'),
                TextColumn::make('dob')->label('Date of Birth'),
                TextColumn::make('gender')->label('Gender')->sortable(),
                TextColumn::make('class_at_entry')->label('Class'),
                TextColumn::make('created_at')->label('Created')->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->visible(auth()->user()->can('super-admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(auth()->user()->can('super-admin')),
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
                TextEntry::make('firstname')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('middle_name')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('lastname')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('email')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('phone')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('entrance_score')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('entry_class.name')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('year_of_entry')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('dob')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('address')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('lga')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('state')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('height')
                    ->label('Height (cm)')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
                TextEntry::make('weight')
                    ->label('Weight (kg)')
                    ->size(TextEntrySize::Large)
                    ->weight(FontWeight::Bold),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissions::route('/'),
            'create' => Pages\CreateAdmission::route('/create'),
            'view' => Pages\ViewAdmission::route('/{record}'),
            'edit' => Pages\EditAdmission::route('/{record}/edit'),
        ];
    }
}
