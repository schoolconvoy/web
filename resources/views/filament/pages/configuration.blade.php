<x-filament-panels::page x-data="{ tab: 'tab1' }">
    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item @click="tab = 'tab1'" :alpine-active="'tab === \'tab1\''">
            Session
        </x-filament::tabs.item>

        <x-filament::tabs.item @click="tab = 'tab2'" :alpine-active="'tab === \'tab2\''">
            Term
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div class="mx-auto w-[--sidebar-width]">
        <div x-show="tab === 'tab1'">
            @livewire('session.create-session')
        </div>
        <div x-show="tab === 'tab2'">
            @livewire('term.create-term')
        </div>
    </div>

</x-filament-panels::page>
