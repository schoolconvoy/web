<?php

namespace App\Livewire\Students;

use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use App\Models\Level;
use Filament\Forms\Get;
use App\Trait\UserTrait;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Admission extends Component implements HasForms
{
    use InteractsWithForms;
    use UserTrait;

    public ?array $data = [];
    private $password;

    public function mount(): void
    {
        $this->form->fill();
    }

    private function getUserLevel()
    {
        // $isHighSchool = auth()->user()->isHighSchool();

        // if ($isHighSchool) {
        //     return Level::where('order', '>', 12)->pluck('name', 'id')->toArray();
        // }

        return Level::where('order', '<', 12)->pluck('name', 'id')->toArray();
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
                                        ->tel(),
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
                                                    ]),
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


    public function create(): Model
    {
        $data = $this->form->getState();

        $studentAndParent = $this->convertParentAndStudentToDualArray($data);

        $this->password = Str::random(8);
        $studentAndParent['student']['password'] = Hash::make($this->password);
        $student = $this->createStudent($studentAndParent['student']);

        if ($studentAndParent['existing_parent']) {
            $this->updateParent($studentAndParent['parent'], $student->id);
            //handle existing parent
            //update parent_ward table
        } else {
            $this->createParent($studentAndParent['parent'], $student->id);
            //handle parent
        }


        return $student;
    }

    public function render(): View
    {
        return view('livewire.students.admission');
    }
}
