<?php

namespace App\Livewire\Term;

use App\Models\Term;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;

class CreateTerm extends Component implements HasForms
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
                    Select::make('name')
                        ->options(Term::generateTerms())
                        ->label('Select term')
                        ->placeholder('Select term')
                        ->searchable()
                        ->required(),
                    Select::make('session_id')
                        ->label('Select session')
                        ->relationship('session', 'year')
                        ->placeholder('Select session')
                        ->preload()
                        ->searchable()
                        ->required()
            ])
            ->statePath('data')
            ->model(Term::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $data['active'] = false;

        if (Term::where('name', $data['name'])->where('session_id', $data['session_id'])->exists()) {
            Notification::make()
                ->title('Term already exists!')
                ->danger()
                ->send();

            return;
        }

        $record = Term::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Term created successfully!')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.term.create-term');
    }
}
