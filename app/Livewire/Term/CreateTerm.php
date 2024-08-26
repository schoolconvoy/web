<?php

namespace App\Livewire\Term;

use App\Models\Term;
use App\Models\Session;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Toggle;

use function Livewire\on;

class CreateTerm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public Session $session;
    protected $terms = array(
        array(
            'name' => '1st Term',
            'start_date' => 'first_start_date',
            'end_date' => 'first_end_date',
            'next_date' => 'first_next_date',
            'status' => 'first_status',
        ),
        array(
            'name' => '2nd Term',
            'start_date' => 'second_start_date',
            'end_date' => 'second_end_date',
            'next_date' => 'second_next_date',
            'status' => 'second_status',
        ),
        array(
            'name' => '3rd Term',
            'start_date' => 'third_start_date',
            'end_date' => 'third_end_date',
            'next_date' => 'third_next_date',
            'status' => 'third_status',
        ),
    );

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $grids = [];

        for($i = 0; $i < count($this->terms); $i++) {
            $grids[] = Fieldset::make($this->terms[$i]['name'])
                                ->schema([
                                    Grid::make(3)->schema([
                                        DatePicker::make($this->terms[$i]['start_date'])
                                            ->label('Start date')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->minDate(function (Get $get, Set $set) use ($i) {
                                                if ($i === 0) {
                                                    return null;
                                                }

                                                return $get($this->terms[$i - 1]['next_date']);
                                            })
                                            ->required(),
                                        DatePicker::make($this->terms[$i]['end_date'])
                                            ->label('End date')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->required(),
                                        DatePicker::make($this->terms[$i]['next_date'])
                                            ->label('Next term start date')
                                            ->native(false)
                                            ->afterStateUpdated(function (Set $set, ?string $state) use ($i) {
                                                if ($i + 1 >= count($this->terms)) {
                                                    return;
                                                }

                                                $next = $this->terms[$i + 1];

                                                if (!$next) {
                                                    return;
                                                }

                                                $field = $next['start_date'];

                                                return $set($field, $state);
                                            })
                                            ->live(onBlur: true)
                                            ->closeOnDateSelection()
                                            ->required(),
                                    ]),
                                    Grid::make(1)->schema([
                                        Toggle::make($this->terms[$i]['status'])
                                                ->label('Active?')
                                                ->onColor('success')
                                                ->offColor('danger')
                                                ->live(true)
                                                ->afterStateUpdated(function(Set $set, $state) use ($i) {
                                                    array_walk($this->terms, function($term) use ($set, $i) {
                                                        if ($term['status'] === $this->terms[$i]['status']) {
                                                            return;
                                                        }

                                                        $set($term['status'], false);
                                                    });
                                                })
                                    ]),
                                ]);
        }

        return $form
            ->schema($grids)
            ->statePath('data')
            ->model(Term::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $data['active'] = false;

        foreach($this->terms as $term) {
            $payload = [
                'name' => $term['name'],
                'start_date' => $data[$term['start_date']],
                'end_date' => $data[$term['end_date']],
                'next_date' => $data[$term['next_date']],
                'active' => $data[$term['status']],
                'session_id' => $this->session->id,
                'school_id' => auth()->user()->school_id,
            ];

            $created = Term::firstOrCreate($payload);

            if ($created->wasRecentlyCreated) {
                Log::debug('Term created successfully!');
            }
        }

        $this->dispatch('close-modal', id: 'create-term-modal');
        $this->dispatch('terms-created');

        Notification::make()
                    ->title('Terms created successfully!')
                    ->success()
                    ->send();
    }

    public function render(): View
    {
        return view('livewire.term.create-term');
    }
}
