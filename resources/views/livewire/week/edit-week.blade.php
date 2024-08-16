<div class="col-md-12">
    <x-filament::modal id="week-edit-modal" width="2xl" :close-button="true">
        <x-slot name="heading">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Edit Week</h2>
        </x-slot>

        {{-- Modal content --}}
        <form wire:submit="edit">
            @if(isset($this->record))
                {{ $this->form }}
            @else
                <x-filament::loading-indicator class="h-5 w-5" />
            @endif
            <x-filament::button
                class="mt-4"
                wire:click="edit"
                tag="button"
            >
                Update
            </x-filament::button>
        </form>

    </x-filament::modal>

    <x-filament-actions::modals />
</div>
