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

class Admission extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    private function getUserLevel()
    {
        $isHighSchool = auth()->user()->isHighSchool();

        if ($isHighSchool) {
            return Level::where('order', '>', 12)->pluck('name', 'id')->toArray();
        }

        return Level::where('order', '<', 12)->pluck('name', 'id')->toArray();
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
                                        'Male' => 'Male',
                                        'Female' => 'Female'
                                    ])
                                    ->required()
                                ,
                                TextInput::make('firstname')
                                    ->required(),
                                TextInput::make('middle name'),
                                TextInput::make('lastname')
                                    ->required(),
                                TextInput::make('email')
                                    ->unique()
                                    ->email(),
                                TextInput::make('phone')
                                    ->tel()
                                ,
                                DatePicker::make('dob')
                                    ->label('Date of birth')
                                    ->required()
                                    ->columns(),
                                Select::make('class_assigned')
                                    ->label('Assign class')
                                    ->options(
                                        $this->getUserLevel()
                                    )
                                    ->nullable(),
                                TextInput::make('height')
                                    ->label('Height'),
                                TextInput::make('weight')
                                    ->label('Weight'),
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
                                    ->required()
                                ,
                                Select::make('parent_title')
                                    ->options([
                                        'mr' => 'Mr',
                                        'mrs' => 'Mrs',
                                        'miss' => 'Miss',
                                        'dr' => 'Dr',
                                        'prof' => 'Prof',
                                    ]),
                                TextInput::make('parent.firstname')
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
                                    ->required()
                                    ->tel(),
                                TextInput::make('parent_state')
                                    ->required()
                                    ->tel(),
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

    public function create(): void
    {
        $data = $this->form->getState();

        Log::debug("Data: " . json_encode($data));

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.students.admission');
    }
}
