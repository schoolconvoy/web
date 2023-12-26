<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    <h1 class="text-center text-2xl">Your results are out!</h1>
    <div class="flex flex-col items-center justify-center">
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">Score</div>
            <div>
                {{ $score }} / {{ $record->total_marks }}
            </div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">Passing marks</div>
            <div>{{ $record->pass_marks }}</div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">Total marks</div>
            <div>{{ $record->total_marks }}</div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">Duration</div>
            <div>
                {{
                    Carbon\CarbonInterval::seconds($record->duration)
                                                            ->cascade()
                                                            ->forHumans()
                }}
            </div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">Start time</div>
            <div>{{ $attempt->created_at->format('h:i:s') }}</div>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="font-bold">End time</div>
            <div>{{ $attempt->updated_at->format('h:i:s') }}</div>
        </div>
    </div>
</x-filament-panels::page>
