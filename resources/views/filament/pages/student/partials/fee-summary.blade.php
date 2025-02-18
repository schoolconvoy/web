@role('Admin|super-admin|Elementary School Principal|High School Principal|Accountant')
    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="fees" role="tabpanel" aria-labelledby="fees-tab">
        <x-filament::section>
            <div class="space-y-6">
                <div class="space-y-4">
                    @if($scholarships->count() > 0)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <h3 class="text-lg font-medium mb-2">Active Scholarships</h3>
                            <div class="space-y-2">
                                @foreach($scholarships as $scholarship)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">{{ $scholarship->title }}</span>
                                        <span class="text-sm font-medium">NGN {{ number_format($scholarship->amount, 2) }}</span>
                                    </div>
                                @endforeach
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between items-center font-medium">
                                        <span>Total Scholarships</span>
                                        <span>NGN {{ number_format($scholarships->sum('amount'), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($record->waivers()->count() > 0)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <h3 class="text-lg font-medium mb-2">Active Fee Waivers</h3>
                            <div class="space-y-2">
                                @foreach($record->waivers()->whereNull('end_date')->orWhere('end_date', '>=', now())->get() as $waiver)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm">{{ $waiver->title }}</span>
                                        <span class="text-sm">
                                            Waived Fees:
                                            @foreach($waiver->fees as $fee)
                                                {{ $fee->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-lg font-medium mb-2">Final Amount Calculation</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span>Total After Discounts</span>
                                <span class="font-medium">NGN {{ number_format($discountedTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Total Scholarships</span>
                                <span class="font-medium text-green-600">- NGN {{ number_format($scholarships->sum('amount'), 2) }}</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between items-center text-lg font-bold">
                                    <span>Final Amount</span>
                                    <span>NGN {{ number_format($finalAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </x-filament::section>
    </div>
@endrole
