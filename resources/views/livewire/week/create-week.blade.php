<div class="col-md-12 flex flex-row justify-end">
    <x-filament::modal width="2xl" :close-button="true" id="create-week-modal">
        <x-slot name="trigger">
            <x-filament::button>
                Create week
            </x-filament::button>
        </x-slot>

        <x-slot name="heading">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Add Week</h2>
        </x-slot>

        {{-- Modal content --}}
        <form wire:submit="create">
            {{ $this->form }}

            <x-filament::button
                class="mt-4"
                wire:click="create"
                tag="button"
            >
                Submit
            </x-filament::button>
        </form>

    </x-filament::modal>

    <x-filament-actions::modals />
</div>
