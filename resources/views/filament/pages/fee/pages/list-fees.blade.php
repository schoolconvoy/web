<x-filament-panels::page>
    @role('super-admin')
        {{ $this->table }}
    @elseif('Parent')
        {{ $this->parentTable() }}
    @endif
</x-filament-panels::page>
