<x-filament-panels::page>
    <x-filament-panels::form wire:submit="create">
        {{ $this->form }}
        <h2>Something about create here</h2>
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>
