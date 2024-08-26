<?php

namespace App\Livewire\Session;

use App\Models\Session;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class CreateSession extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function years()
    {
        $years = range(date('Y') - 20, date('Y'));
        $options = array_combine($years, $years);

        return $options;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('year')
                    ->label('Select a new session')
                    ->options(Session::generateSessions())
                    ->default(Session::active(auth()->user()->school_id)->year ?? null)
                    ->searchable(),
            ])
            ->statePath('data')
            ->model(Session::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        if (Session::where('year', $data['year'])->where('school_id', auth()->user()->school_id)->exists()) {
                Notification::make()
                    ->title('Session already exists!')
                    ->danger()
                    ->send();

            return;
        }

        Session::where('active', true)
                ->where('school_id', auth()->user()->school_id)
                ->update(['active' => false]);

        $data['active'] = true;
        $data['school_id'] = auth()->user()->school_id;

        $record = Session::create($data);

        $this->form->model($record)->saveRelationships();

        Notification::make()
            ->title('Session created successfully!')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'create-session-modal');
    }

    public function render(): View
    {
        return view('livewire.session.create-session');
    }
}
