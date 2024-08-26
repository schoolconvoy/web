<?php

namespace App\Filament\Resources\LessonPlanResource\Pages;

use App\Filament\Resources\LessonPlanResource;
use App\Models\LessonPlan;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use App\Models\Week;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class ListLessonPlans extends ListRecords
{
    protected static string $resource = LessonPlanResource::class;
    protected static string $view = 'filament.resources.lesson-plans.pages.list-plans';
    public $weeks = [];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getWeeks()
    {
        $this->weeks = Week::orderBy('created_at', 'desc')->get();
    }

    public function getLessonPlanType()
    {
        $segments = explode('/', request()->path());
        $type = end($segments);

        return $type;
    }

    public function getPlans()
    {
        return LessonPlan::get();
    }

    #[On('week-created')]
    public function updateWeekList($id)
    {
        $record = Week::find($id);
        $this->weeks = collect($this->weeks)->prepend($record);
    }

    public function mount(): void
    {
        parent::mount();

        $this->getWeeks();
    }
}
