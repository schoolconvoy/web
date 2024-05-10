@if(isset($students) && count($students))
    <div class="flex flex-row gap-3 flex-wrap">
        @foreach($students as $student)
            <div class="border flex flex-col items-center w-3/4 p-6 rounded-md">
                <x-heroicon-m-user-circle class="w-16" />
                @php $ward = App\Models\User::find($student['student']) @endphp
                {{ Log::debug('ward - ' . print_r($ward, true)) }}
                <h2 class="font-semibold">
                    @if (isset($student->firstname))
                        {{ $student->firstname . ' ' . $student->lastname }}
                    @else
                        {{ $ward->firstname . ' ' . $ward->lastname }}
                    @endif
                </h2>
                @if ($ward->admission_no)
                    <p>{{ $ward->admission_no ?? $student->admission_no }}</p>
                @else
                    <p>{{ $ward->admission_no ?? $student->admission_no }}</p>
                @endif
                <button type="button" wire:click="removeWard({{$ward->id ?? $student->id}})" class="bg-gray-200 h-5 left-0 relative rounded-full text-xs w-5">x</button>
            </div>
        @endforeach
    </div>
@endif
