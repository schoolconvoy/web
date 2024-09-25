<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}

    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::button color="gray" icon="heroicon-s-academic-cap" icon-position="after">
                {{ $currentSession->year }} {{ $currentTerm->name }}
            </x-filament::button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach($sessions as $key => $session)
                <x-filament::dropdown.list.item
                    wire:click="setCurrentSession('{{ $key }}')"
                    wire:key="{{ $key }}"
                >
                    {{ $session }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
