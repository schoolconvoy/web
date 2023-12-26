<div>
    <x-filament::modal id="add-teacher" width="2xl">
        <x-slot name="header">
            <h2 class="text-gray-500">
                Assign a new teacher
            </h2>
        </x-slot>

        <div>
            <form wire:submit="log">
                {{ $this->form }}
            </form>
        </div>

        <x-slot name="footerActions">
            {{ $this->saveButtonAction }}
        </x-slot>
    </x-filament::modal>
    <!-- Add teacher component -->

    <!-- List teacher infolist -->
    {{ $this->infolist }}

    <x-filament-actions::modals />
</div>
