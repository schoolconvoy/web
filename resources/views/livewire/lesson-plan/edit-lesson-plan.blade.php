<div class="col-md-12">
    <x-filament::modal width="3xl" :close-button="true" id="reviewLessonPlanModal">
        <x-slot name="trigger">
            <x-filament::button
                outlined
            >
                Edit
            </x-filament::button>
        </x-slot>

        <x-slot name="heading">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Edit lesson plan</h2>
        </x-slot>

        {{-- Modal content --}}
        <form wire:submit="edit">
            {{ $this->form }}

            <x-filament::button
                class="mt-4"
                wire:click="edit"
                tag="button"
            >
                Update
            </x-filament::button>
        </form>

    </x-filament::modal>

    <x-filament-actions::modals />
</div>

