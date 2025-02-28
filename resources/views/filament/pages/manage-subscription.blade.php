<x-filament-panels::page>
    <div class="space-y-6">
        <x-subscription-status :show-details="true" />

        <div class="mt-8">
            {{ $this->form }}
        </div>
    </div>
</x-filament-panels::page>
