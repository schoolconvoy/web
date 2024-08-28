<x-filament-panels::page x-data="{ tab: 'tab1' }">
    <div class="flex flex-row gap-4 w-full">
        <x-filament::tabs label="Content tabs" class="w-1/4 bg-transparent h-1/3 flex-col items-start" style="margin: 0% !important;">
            <x-filament::tabs.item @click="tab = 'tab1'" :alpine-active="'tab === \'tab1\''">
                Session
            </x-filament::tabs.item>

            {{-- <x-filament::tabs.item @click="tab = 'tab2'" :alpine-active="'tab === \'tab2\''">
                Term
            </x-filament::tabs.item> --}}
        </x-filament::tabs>

        <div class="h-96 w-3/4">
            <div x-show="tab === 'tab1'">
                @livewire('session-page')
            </div>
            {{-- <div x-show="tab === 'tab2'">
                @livewire('term.create-term')
            </div> --}}
        </div>
    </div>

</x-filament-panels::page>
