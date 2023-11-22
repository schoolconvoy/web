<div>
    @if(isset($review['student']) && isset($review['bio']))
        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Info Item 2 -->
            <li class="border rounded-md p-3">
                <h3 class="font-semibold text-lg mb-2">{{ $review['bio']['lastname'] ?? '' }}</h3>
                <p>Lastname</p>
            </li>

            <!-- Info Item 1 -->
            <li class="border rounded-md p-3">
                <h3 class="font-semibold text-lg mb-2">Firstname</h3>
                <p>{{ $review['bio']['firstname'] ?? '' }}</p>
            </li>

            <!-- Info Item 2 -->
            <li class="border rounded-md p-3">
                <h3 class="font-semibold text-lg mb-2">{{ $review['bio']['email'] ?? '' }}</h3>
                <p>Email</p>
            </li>


            <!-- Info Item 2 -->
            <li class="border rounded-md p-3">
                <h3 class="font-semibold text-lg mb-2">{{ $review['bio']['phone'] ?? '' }}</h3>
                <p>Phone</p>
            </li>

            <!-- Info Item 2 -->
            <li class="border rounded-md p-3">
                <h3 class="font-semibold text-lg mb-2">{{ $review['bio']['address'] ?? '' }}</h3>
                <p>Address</p>
            </li>
        </ul>

        <div class="flex flex-row gap-3 flex-wrap py-3">
            {{ var_dump($review['student']) }}
            @foreach($review['student'] as $student)
                <div class="border flex flex-col items-center w-3/4 p-6 rounded-md">
                    <x-heroicon-m-user-circle class="w-16" />
                    <h2 class="font-semibold">{{ $student['student']["firstname"] .' '. $student['student']["lastname"] }}</h2>
                    <p>{{ $student['student']['admission_no'] ?? '' }}</p>
                    <button wire:model="parentStudent" class="bg-gray-200 h-5 left-0 relative rounded-full text-xs w-5">x</button>
                </div>
            @endforeach
        </div>
    @endif
</div>

