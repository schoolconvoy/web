<div class="mx-auto bg-white w-full shadow-lg rounded-lg overflow-hidden">
    <div class="border-b-2 border-gray-100 items-center flex flex-row justify-between px-6 py-3 w-full">
        <p class="text-gray-800 text-sm">{{ $plan->class?->name }}</p>
        <div class="flex flex-row justify-end items-center w-1/2">
            <span class="h-3 w-3 rounded-full block" style="background-color: {{ \App\Models\LessonPlan::STATUS_COLORS[$plan->status] }}"></span>
            <p class="text-gray-400 text-sm p-2">
                {{ \App\Models\LessonPlan::STATUS_LABELS[$plan->status] }}
            </p>
        </div>
    </div>
    <div class="flex flex-row justify-between p-6">
        <div class="w-full col-md-3">
            <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $plan->subject?->name }}</h2>
            <span class="text-gray-400">{{ $plan->topics?->first()?->name }}</span>
        </div>
        <div class="col-md- gap-2 flex flex-row items-center justify-end w-full">
            @role('Admin|super-admin|Elementary School Principal|High School Principal')
                <x-filament::button
                    outlined
                    icon="heroicon-o-arrow-down"
                    wire:click="downloadTrigger"
                >
                    Download
                </x-filament::button>
            @endrole
            <x-filament::button
                tag="a"
                href="{{ route('filament.admin.resources.lesson-plans.view-lesson', ['record' => $plan->week->id, 'plan' => $plan]) }}"
            >
                View lesson
            </x-filament::button>
        </div>
    </div>
    <div class="py-4 px-6 flex flex-row justify-between">
        <div class="col-md-6 flex flex-row gap-2">
            <h6 class="font-bold text-gray-800 mb-2 text-sm capitalize">Submitted on</h6>
            <p class="text-gray-600 text-sm">
                {{ $plan->created_at->format('d/m/Y') }}
            </p>
        </div>
        <div class="col-md-6 flex flex-row gap-2">
            <h6 class="font-bold text-gray-800 mb-2 text-sm capitalize">
                <x-heroicon-o-user-circle class="h-5 w-5 text-gray-400" />
            </h6>
            <p class="text-gray-600 text-sm">
                {{ $plan->teacher?->firstname . ' ' . $plan->teacher?->lastname }}
            </p>
        </div>
    </div>
</div>
