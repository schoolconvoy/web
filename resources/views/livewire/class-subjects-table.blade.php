<div>
    <!--Add subjects-->
    <x-filament::modal id="add-subjects" width="2xl">
        <x-slot name="header">
            <h2 class="text-gray-500">
                Add subjects to this class
            </h2>
        </x-slot>

        <div>
            <form wire:submit="assignSubjects">
                {{ $this->form }}
            </form>
        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="assignSubjects" class="float-right">
                Assign subjects
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- Add teacher -->
    <x-filament::modal id="add-teacher" width="2xl">
        <x-slot name="header">
            <h2 class="text-gray-500">
                Assign a teacher for this subject
            </h2>
        </x-slot>

        <div>
            <form wire:submit="assignSubjects">
                {{ $this->teacherForm }}
            </form>
        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="assignTeacher" class="float-right">
                Assign teacher
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <!-- List students table -->
    {{ $this->table }}

    <x-filament-actions::modals />
</div>
