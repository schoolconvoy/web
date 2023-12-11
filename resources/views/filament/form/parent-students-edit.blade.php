@if(isset($students) && count($students))
    <div class="flex flex-row gap-3 flex-wrap">
        @foreach($students as $student)
            <div class="border flex flex-col items-center w-3/4 p-6 rounded-md">
                <x-heroicon-m-user-circle class="w-16" />
                <h2 class="font-semibold">
                    @if (isset($student['student']['firstname']))
                        {{ $student['student']['firstname'] . ' ' . $student['student']['lastname'] }}
                    @endif
                </h2>
                @if (isset($student['student']['admission_no']))
                    <p>{{ $student['student']['admission_no'] }}</p>
                @endif

                @if (isset($student['student']['id']))
                    <button type="button" wire:click="removeWard({{$student['student']['id']}})" class="bg-gray-200 h-5 left-0 relative rounded-full text-xs w-5">x</button>
                @endif
            </div>
        @endforeach
    </div>
@endif
