<div>
    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button
            class="mt-4"
            wire:click="create"
            tag="button"
        >
            Save
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
