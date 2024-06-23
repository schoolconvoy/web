<?php

namespace App\Livewire;

use App\Models\UserMeta;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class WardSwitcher extends Component
{
    public $wards = [];
    public $selectedWard = null;

    public function __construct()
    {

    }

    public function getWards()
    {
        $children = auth()->user()->wards;

        if (!Cache::has('ward'))
        {
            if ($children->count() > 0) {
                Cache::put('ward', $children[0]->id);
            }
        }

        $this->wards = $children;
        $this->selectedWard = Cache::get('ward');
    }

    public function render()
    {
        $this->getWards();

        return view('livewire.ward-switcher');
    }
}
