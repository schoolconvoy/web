<div>
    <x-filament::modal width="3xl" :close-button="true" id="create-term-modal">
        <x-slot name="trigger">
            <x-filament::button
                class="text-right w-full"
            >
                Add terms
            </x-filament::button>
        </x-slot>

        <x-slot name="heading">
            <h2 class="text-xl font-bold text-gray-800 mb-2">
                {{ $session?->year }} session:
                Configure new term
            </h2>
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
