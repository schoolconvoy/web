<?php

namespace App\Livewire;

use Livewire\Component;

class LessonItem extends Component
{
    public $teacher;
    public $subject;
    public $topic;
    public $plan;
    public $created_at; // submitted_at

    public function downloadTrigger()
    {
        return response()->download(storage_path('app/public/' . $this->plan->files));
    }

    public function render()
    {
        return view('livewire.lesson-item');
    }
}
