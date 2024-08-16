<div class="mx-auto bg-white w-full shadow-lg rounded-lg ove`rflow-hidden">
    <div class="flex flex-row justify-between p-6">
        <div class="col-md-3">
            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $week->name }}</h2>
            <span class="text-gray-400">{{ $week->term->name }} {{ $week->session->year }}</span>
        </div>
        <div class="col-md-9">
            <p class="flex flex-row gap-2 text-gray-600 mb-4">
                <x-heroicon-o-calendar class="h-6 w-6 text-gray-400" />
                <span class="text-gray-400">{{ $lessonPlansCount }} lesson plans</span>
            </p>
        </div>
    </div>
    <div class="py-4 px-6 bg-gray-100 flex flex-row justify-between">
        <div class="col-md-6 flex flex-row gap-2">
            <div>
                <h6 class="font-bold text-gray-800 mb-2 text-sm capitalize">Start Date</h6>
                <p class="text-gray-600 text-sm">
                    {{ $week->start_date->format('d/m/Y') }}
                </p>
            </div>
            <div>
                <h6 class="font-bold text-gray-800 mb-2 text-sm capitalize">End Date</h6>
                <p class="text-gray-600 text-sm">
                    {{ $week->end_date->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="col-md-6">
            @role('Admin|super-admin|Elementary School Principal|High School Principal')
                <x-filament::button
                    wire:click="openEditModal({{ $week->id }})"
                    outlined
                    tag="button"
                >
                    Edit week
                </x-filament::button>
            @endrole
            <x-filament::button
                href="{{ route('filament.admin.resources.lesson-plans.'.$type, $week->id) }}"
                tag="a"
            >
                View week
            </x-filament::button>
        </div>
    </div>
</div>
