<div>
    <x-filament::dropdown>
        <x-slot name="trigger">
            <x-filament::link
                tag="button"
            >
                Switch ward
            </x-filament::link>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach($wards as $ward)
                    <x-filament::dropdown.list.item>
                        <x-filament::link
                            :color="$selectedWard === $ward->id ? 'orange' : 'gray'"
                            :href="route('wards.switch', [ 'id' => $ward->id ])"
                            icon="heroicon-m-users"
                            :underline="false"
                        >
                            {{ $ward->firstname . ' ' . $ward->lastname }}
                        </x-filament::link>
                    </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
