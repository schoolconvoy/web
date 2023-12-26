<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    <div class="grid grid-flow-col py-5 gap-4">
        <div class="w-8/12 grid gap-y-8">
            <form method="POST" wire:submit="handleSubmission" id="submitQuiz">
                @foreach($record->questions as $index => $quiz_question)
                    <div class="mb-4" id="question-{{$quiz_question->id}}">
                        <div class="mb-4">
                            <h1 class="text-xl font-bold flex gap-1">
                                Q{{ $index + 1 }}.
                                {!! $quiz_question->question->name !!}
                            </h1>
                            @if($quiz_question->question->media_url)
                                <img src="{{ asset('/storage/'. $quiz_question->question->media_url) }}" class="h-32 pt-4 px-6 w-auto" alt="">
                            @endif
                        </div>
                        <div class="flex flex-row flex-wrap items-center justify-between max-w-prose mb-4">
                            @foreach($quiz_question->question->options as $option)
                                <div class="flex items-center">
                                    <input
                                        id="{{ $option->id }}"
                                        type="radio"
                                        value="{{ $option->id }}"
                                        wire:model="answers.{{ $quiz_question->id }}"
                                        class="w-4 h-4 text-blue-600 bg-gray-100
                                                border-gray-300
                                                dark:ring-offset-gray-800 focus:ring-2
                                                dark:bg-gray-700 dark:border-gray-600">
                                    <label for="{{ $option->id }}" class="ms-4 px-1 font-medium text-gray-900 dark:text-gray-300 text-lg">{!! $option->name !!}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </form>
        </div>

        <div class="w-4/12 top-6 sticky max-w-xs h-96 max-h-96">
            <div class="flex flex-col gap-6 items-center">
                <!--Mark and attempt details-->
                <div class="flex flex-row gap-1">
                    <x-heroicon-m-document-check class="h-5 mr-2" />
                    <p class="font-medium text-sm">Max mark: </p>
                    <span class="text-sm font-medium text-blue-500">{{ $record->total_marks }}</span>
                </div>

                <div class="flex flex-row gap-1">
                    <x-heroicon-o-arrow-uturn-left class="h-5 mr-2" />
                    <p class="font-medium text-sm">Max attempts: </p>
                    <span class="text-sm font-medium text-blue-500">
                        {{ $record->attempts()->where('participant_id', auth()->user()->id)->count() }}
                        of
                        {{ $record->max_attempts }}
                    </span>
                </div>

                <!--Duration-->
                <div class="border-2 border-r-indigo-500 flex flex-col justify-center h-32 items-center p-6 rounded-full w-32">
                    <p class="font-medium text-xs">Time</p>
                    <span id="timer" class="text-xl font-medium text-blue-500"></span>
                </div>

                <!--Question nav-->
                <div class="flex flex-col justify-center items-center">
                    <h2 class="text-gray-700 font-medium mb-2">Question panel</h2>
                    <ul class="flex flex-wrap gap-4 items-center">
                        @foreach($record->questions as $index => $quiz_question)
                            <li class="bg-gray-300 flex h-7 items-center justify-center rounded-full text-center w-7">
                                <a href="#question-{{ $quiz_question->id }}" class="text-gray-700 font-medium">{{ $index+1 }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!--Submit button-->
                <div class="flex flex-col justify-center items-center">
                    <x-filament::button
                        wire:click="handleSubmission"
                        icon="heroicon-m-check-circle"
                        icon-position="after"
                    >
                        Submit
                    </x-filament::button>
                    <span class="text-sm text-gray-400 font-medium py-2">Will auto-submit once time is up</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onbeforeunload = function() {
            return "Are you sure you want to leave?";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script>
        const now = dayjs(); // Get the current time
        const futureTime = now.add('{{ $record->duration }}', 'second'); // Calculate future time

        const interval = setInterval(() => {
            const currentTime = dayjs();

            // Calculate the remaining time in seconds
            const remainingSeconds = futureTime.diff(currentTime, 'second');

            if (remainingSeconds <= 0) {
                clearInterval(interval); // Stop the countdown when time is up
                // submit form
                // document.getElementById('submitQuiz')
                // TODO: Submit when the time is up

            } else {
                // Calculate hours, minutes, and seconds
                const hours = Math.floor(remainingSeconds / 3600);
                const minutes = Math.floor((remainingSeconds % 3600) / 60);
                const seconds = remainingSeconds % 60;

                // Format the countdown into HH:MM:SS
                const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Display the countdown
                document.getElementById('timer').innerText = `${formattedTime}`;
            }
        }, 1000);
    </script>
</x-filament-panels::page>
