<?php

namespace App\Livewire;

use App\Models\Fee;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class ListParentWardFees extends Component
{
    public $parentId;
    public $fees;
    public $wards;

    public function render()
    {
        $wards = User::find($this->parentId)->wards()->get();
        $this->wards = $wards->pluck('id');

        $fees = Fee::whereHas('students', function ($query) use ($wards) {
            $query->whereIn('student_id', $wards->pluck('id'));
        });

        $this->fees = $fees->get();

        return view('livewire.list-parent-ward-fees');
    }
}
