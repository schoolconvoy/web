<div>
    <h2 class="text-xl font-semibold mb-5">Sessions and terms</h2>
    <div class="container">
        <div class="items-center flex flex-row justify-between my-4">
            <div class="flex flex-col">
                <h5 class="text-base text-gray-500">Current Session</h5>
                <p class="font-semibold text-base">{{ $session->year }} session</p>
            </div>
            @livewire('session.create-session')
        </div>
        <div class="body">
            <div class="relative overflow-x-auto border border-s">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Term
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Time frame
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($session->terms) > 0)
                            @foreach($session->terms as $term)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700" wire:key="{{ $term->name }}">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $term->name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $term->start_date }} &mdash; {{ $term->end_date }}
                                    </td>
                                    <td class="flex flex-row gap-2 items-center px-6 py-4">
                                        <span x-show="(bool){{ $term->active }}" class="flex w-3 h-3 me-3 bg-green-500 rounded-full" style="background-color: green"></span>
                                        {{ $term->active ? 'Current' : '' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @livewire('term.edit-term', ['record' => $term])
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="4" class="text-center py-4 px-6">
                                    <p class="my-4">No terms configured for this session.</p>
                                    @livewire('term.create-term', ['session' => $session])
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
