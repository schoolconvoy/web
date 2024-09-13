<x-filament-panels::page>
    <h1 class="title">
        {{ $record->class->name }}
    </h1>
    <h3 class="text-muted">{{ $record->subject->name }}</h3>

    @livewire('result.view-result', ['record' => $record])

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>
