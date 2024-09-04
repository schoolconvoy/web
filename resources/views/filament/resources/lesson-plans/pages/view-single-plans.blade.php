<x-filament-panels::page>
    <div class="w-full border-b items-center flex flex-row justify-between">
        <!--- Lesson Plan Title --->
        <div class="flex flex-col justify-between pb-6 w-1/2">
            <span class="bg-gray-200 block max-w-fit mb-2 px-4 py-0.5 text-black text-sm">{{ $this->plan->week->name }}</span>
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $this->plan->subject->name }}</h2>
            <span class="text-gray-400">{{ $this->plan->class->name }}</span>
        </div>

        <!--- Lesson Plan Actions --->
        <div class="flex flex-row justify-between gap-3">
            @role('Admin|super-admin|Elementary School Principal|High School Principal')
                <x-filament::button wire:click="approvePlan">
                    Approve
                </x-filament::button>
                @livewire('lesson-plan.review-lesson-plan', ['lessonPlan' => $this->plan])
            @endrole

            @if ($this->plan->status !== 'approved' && $this->plan->teacher_id === auth()->user()->id)
                @livewire('lesson-plan.edit-lesson-plan', ['record' => $this->plan], key('edit-lesson-plan-'.$this->plan->id))
            @endif
        </div>
    </div>


    <!--- Lesson Plan Info List --->
    <div class="flex flex-col gap-8">
        <ul class="gap-4 grid grid-flow-col">
            <li class="flex flex-col">
                <span class="font-semibold">Topic</span>
                <span>{{ $this->plan->topics?->first()?->name }}</span>
            </li>
            <li class="flex flex-col">
                <span class="font-semibold">Period</span>
                <span>{{ $this->plan->period }}</span>
            </li>
            <li class="flex flex-col">
                <span class="font-semibold">Duration</span>
                <span>{{ $this->plan->duration }}</span>
            </li>
        </ul>

        <!---- Lesson Plan Summary --->
        <div class="bg-white mx-auto flex flex-col overflow-hidden px-6 py-8 rounded-lg shadow-sm w-full">
            <span class="font-semibold text-sm">Summary</span>
            <span>{{ $this->plan->objectives }}</span>
        </div>

        <!---- Lesson Plan Content Download --->
        <div class="flex flex-row justify-between">
            <x-filament::button
                outlined
                tag="button"
                wire:click="downloadTrigger"
                icon="heroicon-o-arrow-down"
            >
                Download
            </x-filament::button>
        </div>

        <!---- Lesson Plan Review Timeline --->
        <div class="bg-white mx-auto flex flex-col overflow-hidden py-8 rounded-lg shadow-sm w-full" id="lesson-plan-review">
            <p class="font-semibold text-sm pb-6 px-6">Reviews</p>
            @foreach($this->plan->reviews as $review)
                <div
                    class="flex flex-row justify-between items-center py-6 mx-8 px-6"
                    x-data="{
                        done{{ $review->id }}: {{ (int) $review->status }}
                    }"
                    :style="{ textDecoration: done{{ $review->id }} ? 'line-through' : 'unset' }"
                    :class="{ 'bg-gray-100': done{{ $review->id }} }"
                >
                    <!--<x-filament::loading-indicator class="h-5 w-5" x-show="loading{{ $review->id }}" />-->
                    <div class="flex flex-row items-center gap-4">
                        @if ($this->plan->teacher_id === auth()->user()->id)
                            <x-filament::input.checkbox x-model="done{{ $review->id }}" :checked="$review->status" wire:change="updateStatus({{ $review->id }})" />
                        @endif
                        <div>
                            <span>{{ $review->reviewer?->firstname . ' ' . $review->reviewer?->lastname }}</span>
                            <span class="text-gray-500 text-sm">{{ $review->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                    <span>{{ $review->comment }}</span>
                </div>
                <hr class="border-b border-gray-100">
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
