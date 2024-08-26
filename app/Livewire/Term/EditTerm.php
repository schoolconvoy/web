<?php

namespace App\Livewire\Term;

use App\Models\Term;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;

class EditTerm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Term $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Edit Term')
                        ->schema([
                            Grid::make(3)
                                    ->schema([
                                        Forms\Components\DatePicker::make('start_date')
                                            ->label('Start Date')
                                            ->required(),
                                        Forms\Components\DatePicker::make('end_date')
                                            ->label('End Date')
                                            ->required(),
                                        Forms\Components\DatePicker::make('next_date')
                                            ->label('Next Start Date')
                                            ->required(),
                                    ]),
                            Forms\Components\Toggle::make('active')
                                    ->label('Active')
                                    ->required(),
                        ])
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function edit(): void
    {
        $data = $this->form->getState();

        // Set all the terms associated with this session to inactive
        if($data['active'] === true) {
            $this->record->session->terms()->update(['active' => false]);
        }

        $this->record->update($data);

        Notification::make()
            ->title('Terms updated successfully!')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'edit-term-modal');
    }

    public function render(): View
    {
        return view('livewire.term.edit-term');
    }
}
