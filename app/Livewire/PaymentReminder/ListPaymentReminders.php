<?php

namespace App\Livewire\PaymentReminder;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\On;

class ListPaymentReminders extends Component
{
    public $parentId;
    public $reminders;

    public function mount()
    {
        $this->reminders = User::find($this->parentId)->paymentReminders;

        Log::debug("Reminders " . print_r($this->reminders, true) . " parent id " . $this->parentId);
    }

    #[On('reminder-created')]
    public function updatedParentId($value)
    {
        $this->reminders = User::find($value)->paymentReminders;
    }

    public function render()
    {
        return view('livewire.list-payment-reminders');
    }
}
