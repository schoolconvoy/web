<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="my-16 relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Fee
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Ward
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $fee)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $fee->name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ '₦' . number_format($fee->amount, 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $fee->payments()->count() > 0 ? "Paid" : "Outstanding" }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $fee->students()->whereIn('student_id', $wards)->get()->pluck('firstname')->implode(', ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
