<div>
    <ol class="relative border-s border-gray-200 dark:border-gray-700">
        @foreach($reminders as $reminder)
            <li class="mb-10 ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <img class="rounded-full shadow-lg" src="{{ $reminder->sentBy->getFilamentAvatarUrl() }}" alt="{{ $reminder->sentBy->fullname }}"/>
                </span>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                    <div class="items-center justify-between mb-3 sm:flex">
                        <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">
                            {{ $reminder->created_at->diffForHumans() }}
                        </time>
                        <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">{{ $reminder->sentBy->fullname }} sent a reminder for {{ $reminder->student->fullname }}</div>
                    </div>
                    <div class="p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">
                        {{ $reminder->message }}
                    </div>
                </div>
            </li>
        @endforeach
    </ol>
    @livewire(App\Livewire\PaymentReminder\CreatePaymentReminder::class, ['parentId' => $parentId])
</div>
