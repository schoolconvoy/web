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
                <h3 class="font-semibold text-lg mb-2">{{ $review['bio']['firstname'] ?? '' }}</h3>
                <p>Firstname</p>
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
            @foreach($review['student'] as $student)
                <div class="border flex flex-col items-center w-3/4 p-6 rounded-md">
                    @if(in_array('firstname', $student))
                        @php
                            $firstname = $student['firstname'];
                            $lastname = $student['lastname'];
                            $admission_no = $student['admission_no'];
                            $student_id = $student['id'];
                        @endphp
                        <x-heroicon-m-user-circle class="w-16" />
                        <h2 class="font-semibold">{{ $firstname .' '. $lastname }}</h2>
                        <p>{{ $admission_no ?? '' }}</p>
                        <button
                            type="button"
                            wire:click="removeWard({{$student_id}})"
                            class="bg-gray-200 h-5 left-0 relative rounded-full text-xs w-5"
                        >x</button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

