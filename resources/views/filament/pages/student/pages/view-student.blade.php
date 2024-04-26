<x-filament-panels::page>
    <!-- Tab links -->
    <div class="h-20 w-20 rounded-full overflow-hidden">
        <img src="{{ $record->picture ? asset('/storage/'. $record->picture) : null }}" alt="{{ $record->firstname }}">
    </div>
    <div class="flex gap-3 items-center py-3">
        <div class="flex flex-row gap-x-2.5">
            <x-heroicon-m-users class="h-5" />
            <h1 class="text-sm font-bold text-gray-800">
                {{ $record->admission_no }}
            </h1>
        </div>
        <div class="flex flex-row gap-x-2.5">
            <x-heroicon-s-academic-cap class="h-5" />
            <h1 class="text-sm font-bold text-gray-800">
                {{ $record->class->name ?? '' }}
            </h1>
        </div>
    </div>
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profile</button>
            </li>
        </ul>
    </div>
    <div id="default-tab-content">
        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            {{ $this->infolist }}
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
