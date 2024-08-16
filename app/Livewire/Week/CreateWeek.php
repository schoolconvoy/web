<?php

namespace App\Livewire\Week;

use App\Models\Session;
use App\Models\Term;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Week;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Notifications\Notification;

class CreateWeek extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Week')
                    ->placeholder('Enter week name')
                    ->required(),
                Grid::make(2)
                    ->schema([
                        Select::make('session_id')
                            ->label('Session')
                            ->options(Session::pluck('year', 'id'))
                            ->searchable()
                            ->native(false)
                            ->required(),
                        Select::make('term_id')
                            ->label('Term')
                            ->options(function(Get $get) {
                                return Term::where('session_id', $get('session_id'))->pluck('name', 'id');
                            })
                            ->native(false)
                            ->searchable()
                            ->required(),
                    ]),
                Select::make('order')
                    ->placeholder('Select week order')
                    ->label('Order')
                    ->options([
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                        7 => 7,
                        8 => 8,
                        9 => 9,
                        10 => 10,
                        11 => 11,
                        12 => 12,
                        13 => 13,
                        14 => 14,
                        15 => 15,
                        16 => 16,
                        17 => 17,
                        18 => 18,
                    ])
                    ->searchable()
                    ->required(),
                DateRangePicker::make('start_end_date')
                    ->label('Start date / End date')
                    ->startDate(Carbon::now())
                    ->endDate(Carbon::now()),
            ])
            ->statePath('data')
            ->model(Week::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $split_date = explode(" - ", $data['start_end_date']);

        $data['start_date'] = $split_date[0];
        $data['start_date'] = Carbon::createFromFormat('d/m/Y', $data['start_date'])->toDateString();

        $data['end_date'] = $split_date[1];
        $data['end_date'] = Carbon::createFromFormat('d/m/Y', $data['end_date'])->toDateString();

        // Remove hyphenated date
        unset($data['start_end_date']);

        // If the week order already exists for the same term and session, throw an error
        if(Week::where('order', $data['order'])->where('term_id', $data['term_id'])->where('session_id', $data['session_id'])->exists()) {
            Notification::make()
                        ->title('This week already exists for the selected term and session')
                        ->danger()
                        ->send();
            return;
        }

        $record = Week::create($data);

        Notification::make()
            ->title('Week created successfully')
            ->success()
            ->send();

        $this->form->model($record)->saveRelationships();

        $this->dispatch('week-created', id: $record->id);
        $this->dispatch('close-modal', id: 'create-week-modal');

    }

    public function render(): View
    {
        return view('livewire.week.create-week');
    }
}
