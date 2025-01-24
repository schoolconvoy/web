<x-filament-panels::page>
    <!-- Tab links -->
    <div class="h-20 w-20 rounded-full overflow-hidden">
        <img src="{{ $record->picture ? asset('/storage/'. $record->picture) : null }}" alt="{{ $record->firstname }}">
    </div>
    <div class="flex gap-3 items-center py-3">
        <div class="flex flex-row gap-x-2.5">
            <x-heroicon-m-users class="h-5" />
            <h1 title="Admission number" class="text-sm font-bold text-gray-800">
                {{ $record->admission_no }}
            </h1>
        </div>
        <div class="flex flex-row gap-x-2.5">
            <x-heroicon-s-academic-cap class="h-5" />
            <h1 title="Class" class="text-sm font-bold text-gray-800">
                {{ $record->class->name ?? '' }}
            </h1>
        </div>
        <div class="flex flex-row gap-x-2.5">
            <x-heroicon-c-calendar-days class="h-5" />
            <h1 title="Year of entry" class="text-sm font-bold text-gray-800">
                {{ $record->year_of_entry ?? '' }}
            </h1>
        </div>
    </div>
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="fees-tab" data-tabs-target="#fees" type="button" role="tab" aria-controls="fees" aria-selected="false">Fees</button>
            </li>
        </ul>
    </div>
    <div id="default-tab-content">
        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            {{ $this->infolist }}
        </div>
        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="fees" role="tabpanel" aria-labelledby="fees-tab">
            <!-- insert table here -->
            {{ $this->table }}
        </div>
    </div>


    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabsElement = document.getElementById('default-tab');

    const tabElements = [
        {
            id: 'profile',
            triggerEl: document.querySelector('#profile-tab'),
            targetEl: document.querySelector('#profile'),
        },
        {
            id: 'fees',
            triggerEl: document.querySelector('#fees-tab'),
            targetEl: document.querySelector('#fees'),
        },
    ];

    const options = {
        defaultTabId: 'profile',
        activeClasses: 'text-blue-600 border-b-2 border-blue-600',
        inactiveClasses: 'hover:text-gray-600 border-transparent',
    };

    const tabs = new Tabs(tabsElement, tabElements, options);

    // Read ?tab=... from the URL
    const url = new URL(window.location.href);
    const targetTabParam = url.searchParams.get('tab'); // e.g. "settings"

    console.log({ targetTabParam, tabs })

    // If we have a tab param, show it
    if (targetTabParam) {
            // We assume each tab's ID is something like "fees-tab"
            // so we append "-tab" to the param
            tabs.show(`${targetTabParam}`);
    }
});
</script>
