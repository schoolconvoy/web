<x-filament::page>
    <x-filament::section>
        <x-slot name="heading">
            Manage Subscriptions
        </x-slot>

        <x-slot name="description">
            Manage your subscription plans and billing details.
        </x-slot>

        @if($tenant)
            <div class="mb-6">
                <h3 class="text-lg font-medium">Current Tenant: {{ $tenant->name }}</h3>
                <p class="text-sm text-gray-500">
                    @if($tenant->plan)
                        Current Plan: <span class="font-medium">{{ $tenant->plan->name }}</span>
                    @else
                        No active plan
                    @endif
                </p>
            </div>
        @else
            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h3 class="text-lg font-medium text-yellow-800">No Tenant Selected</h3>
                <p class="text-sm text-yellow-600">
                    You are viewing this page as a super admin. Please select a tenant to manage their subscriptions.
                </p>
            </div>
        @endif
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Current Subscriptions
        </x-slot>

        {{ $this->getTenantSubscriptionsTable() }}
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Subscribe to a Plan
        </x-slot>

        <x-slot name="description">
            Choose a plan and payment method to subscribe.
        </x-slot>

        <form wire:submit="subscribe">
            {{ $this->getSubscribeForm() }}

            <div class="mt-4">
                <x-filament::button type="submit">
                    Subscribe
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>
