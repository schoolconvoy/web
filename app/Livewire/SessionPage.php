<?php

namespace App\Livewire;

use App\Models\Session;
use Livewire\Component;
use Livewire\Attributes\On;

class SessionPage extends Component
{
    public $session;

    public function mount(): void
    {
        $this->session = Session::active(auth()->user()->school_id);
    }

    #[On('terms-created')]
    public function updateTermList($id)
    {
        $this->session = Session::active(auth()->user()->school_id);
    }

    public function render()
    {
        return view('livewire.session-page');
    }
}
