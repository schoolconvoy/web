<x-filament-panels::page x-data="{ tab: 'my_lesson_plans' }">

    <x-filament::tabs label="Content tabs">
        @role('Admin|super-admin|Elementary School Principal|High School Principal')
            <x-filament::tabs.item @click="tab = 'all_weeks'" :alpine-active="'tab === \'all_weeks\''">
                All Weeks
            </x-filament::tabs.item>
        @endrole

        <x-filament::tabs.item @click="tab = 'awaiting_review'" :alpine-active="'tab === \'awaiting_review\''">
            Awaiting Review
        </x-filament::tabs.item>

        <x-filament::tabs.item @click="tab = 'approved_lesson_plans'" :alpine-active="'tab === \'approved_lesson_plans\''">
            Approved Lesson Plans
        </x-filament::tabs.item>

        <x-filament::tabs.item @click="tab = 'my_lesson_plans'" :alpine-active="'tab === \'my_lesson_plans\''">
            My Lesson Plans
        </x-filament::tabs.item>
    </x-filament::tabs>

    @role('Admin|super-admin|Elementary School Principal|High School Principal')
        @livewire('week.create-week')
    @endrole

    <!--- Edit Week Modal -->
    @livewire('week.edit-week')

    @role('Admin|super-admin|Elementary School Principal|High School Principal')
        <div x-show="tab === 'all_weeks'">
            <div class="grid grid-cols-2 gap-4">
                @foreach ($weeks as $week)
                    <livewire:week-item :week="$week" :key="'all_weeks_' . $week->id" :lesson-plans-count="0" type="view" />
                @endforeach
            </div>
        </div>
    @endrole

    <div x-show="tab === 'awaiting_review'">
        <div class="grid grid-cols-2 gap-4">
            @foreach ($weeks as $week)
                <livewire:week-item :week="$week" :key="'pending_' . $week->id" :lesson-plans-count="0" type="pending" />
            @endforeach
        </div>
    </div>

    <div x-show="tab === 'approved_lesson_plans'">
        <div class="grid grid-cols-2 gap-4">
            @foreach ($weeks as $week)
                <livewire:week-item :week="$week" :key="'approved_' . $week->id" :lesson-plans-count="0" type="approved" />
            @endforeach
        </div>
    </div>

    <div x-show="tab === 'my_lesson_plans'">
        <div class="grid grid-cols-2 gap-4">
            @foreach ($weeks as $week)
                <livewire:week-item :week="$week" :key="'mine_' . $week->id" :lesson-plans-count="0" type="mine" />
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
