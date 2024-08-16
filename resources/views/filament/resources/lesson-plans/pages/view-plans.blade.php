<x-filament-panels::page>
    <div class="flex flex-row justify-end">
        @livewire('lesson-plan.create-lesson-plan', ['week' => $this->record->id])
    </div>
    <div class="grid grid-cols-2 gap-4">
        @foreach ($this->lessonPlans as $plan)
            @livewire('lesson-item', ['plan' => $plan], key($plan->id))
        @endforeach
    </div>
</x-filament-panels::page>
