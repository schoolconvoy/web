<?php

namespace App\Livewire\Students;

use App\Events\ParentCreated;
use App\Filament\Resources\AdmissionResource\Pages\CreateAdmission;
use App\Filament\Resources\AdmissionResource\Pages\EditAdmission;
use App\Filament\Resources\AdmissionResource\Pages\ListAdmissions;
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
use App\Trait\UserTrait;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Admission extends Component implements HasForms
{
    use InteractsWithForms;
    use UserTrait;
    //use a trait
    // only add admission no and parent no when sudents have paid

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Bio data')
                        ->icon('heroicon-s-user-circle')
                        ->description('Add basic information about student here')
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
                                        ->unique()
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
                                    Textarea::make('address')
                                        ->required()
                                        ->maxLength(200)
                                ]),
                        ]),
                    Step::make('Add guardian/parent')
                        ->icon('heroicon-s-user-circle')
                        ->description('Add parent information')
                        ->schema([
                            Grid::make([
                                'sm' => 2,
                                'xl' => 2,
                                '2xl' => 2,
                            ])
                                ->schema([
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
                                    Textarea::make('parent_address')
                                        ->required()
                                        ->maxLength(200),
                                    TextInput::make('parent_lga')
                                        ->required(),
                                    TextInput::make('parent_state')
                                        ->required(),
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
    public function extractParentInfo(array $data)
    {
        // Filter out elements that start with "parent_"
        $parentData = array_filter($data, function ($key) {
            return strpos($key, 'parent_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        // Remove parent elements from the original array
        $remainingData = array_diff_key($data, $parentData);

        // Remove the "parent_" prefix from the keys in the parentData array
        $parentData = array_combine(
            array_map(function ($key) {
                return substr($key, 7); // Remove the first 7 characters ("parent_")
            }, array_keys($parentData)),
            $parentData
        );
        $parentData['school_id'] = 1; //this is a default available school
        $remainingData['school_id'] = 1;
        return [
            'parent' => $parentData,
            'student' => $remainingData
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $parentAndStudent = $this->convertParentAndStudentToDualArray($data);
        // $parent = $this->parentHandler(($parentAndStudent['parent']));
        $student = $this->studentHandler(($parentAndStudent['student']));
    }
    public function parentHandler($data)
    {
        $parent = $this->createStudent($data);
    }
    public function studentHandler($data)
    {
        $student = $this->createStudent($data);
    }
    public function render(): View
    {
        return view('livewire.students.admission');
    }
}
