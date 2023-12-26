<div>
    <x-filament::modal id="add-students" width="5xl">
        <x-slot name="header">
            <h2 class="text-gray-500">
                Add students
            </h2>
        </x-slot>

        <div>
            <form wire:submit="log">
                {{ $this->form }}
            </form>

            @if(isset($students) && count($students))
                <div class="flex flex-row gap-3 flex-wrap">
                    @foreach($students as $key => $student)
                        <div class="border flex flex-col items-center w-3/4 p-6 rounded-md">
                            <x-heroicon-m-user-circle class="w-16" />
                            <h2 class="font-semibold">{{ $student->firstname .' '. $student->lastname }}</h2>
                            <p>{{ $student->meta()->where('key', 'admission_no')->first()->value ?? '' }}</p>
                            <button wire:click="removeStudent({{ $key }})" class="bg-gray-200 h-5 left-0 relative rounded-full text-xs w-5">x</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="saveStudents">
                Save
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
    <!-- Add students component -->

    <!-- List students table -->
    {{ $this->table }}

    <x-filament-actions::modals />
</div>
