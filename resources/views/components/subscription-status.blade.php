<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
    @if(!$tenant || !$tenant->subscription)
        <div class="text-center">
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">No Active Subscription</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Please subscribe to a plan to continue using all features.</p>
            <a href="{{ route('filament.admin.pages.subscription') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                View Plans
            </a>
        </div>
    @else
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $tenant->plan->name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        @if($remainingDays > 0)
                            {{ $remainingDays }} days remaining
                        @else
                            Subscription expired
                        @endif
                    </p>
                </div>
                @if($remainingDays <= 7)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $remainingDays <= 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $remainingDays <= 0 ? 'Expired' : 'Expiring Soon' }}
                    </span>
                @endif
            </div>

            @if($showDetails && !empty($usageStats))
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Usage Statistics</h4>
                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        @foreach(['students', 'teachers', 'classes'] as $resource)
                            <div class="relative pt-1">
                                <div class="flex mb-2 items-center justify-between">
                                    <div>
                                        <span class="text-xs font-semibold inline-block text-gray-600 dark:text-gray-400 uppercase">
                                            {{ ucfirst($resource) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-semibold inline-block text-gray-600 dark:text-gray-400">
                                            {{ $usageStats[$resource]['used'] }}/{{ $usageStats[$resource]['total'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
                                    @php
                                        $percentage = ($usageStats[$resource]['total'] > 0)
                                            ? ($usageStats[$resource]['used'] / $usageStats[$resource]['total']) * 100
                                            : 0;
                                    @endphp
                                    <div style="width: {{ $percentage }}%"
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center
                                                {{ $percentage >= 90 ? 'bg-red-500' : ($percentage >= 75 ? 'bg-yellow-500' : 'bg-green-500') }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('filament.admin.pages.manage-subscription') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                    Manage Subscription →
                </a>
            </div>
        </div>
    @endif
</div>
