<div>
    <!-- Link to Google Classroom -->
    {{ $this->form }}

    <div class="py-3 text-right">
        <x-filament::button
            wire:click="saveGoogleClassroom"
            size="lg"
        >
            Save
        </x-filament::button>
    </div>

    <x-filament-actions::modals />
</div>
