<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>

    <div class="flex flex-col items-center justify-center">
        <div class="w-8/12 grid gap-y-8">
            <h2>Your results are out!</h2>
            
        </div>
    </div>

    <script>
        // window.onbeforeunload = function() {
        //     return "Are you sure you want to leave?";
        // }
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
                document.getElementById('timer').innerText = 'Countdown expired!';
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
