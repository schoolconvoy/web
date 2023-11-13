<div>
    <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        {{--  TODO: Tab table and users list  --}}
        <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10 !border-t-0 h-screen">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="dark:bg-white/5">
                <tr class="dark:bg-white/5 bg-gray-50 sticky top-0 left-0 z-10">
                    <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-actions">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span class="fi-ta-header-cell-label text-sm font-normal text-gray-950 dark:text-white">
                                    Actions
                                </span>
                            </span>
                    </th>
                    @foreach($this->getRoles() as $role)
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6 fi-table-header-cell-actions">
                                <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                    <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ $role }}
                                    </span>
                                </span>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                @foreach($this->getPermissions() as $permissionGroup)
                    <tr class="bg-gray-50 fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                        <td colspan="10" class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-actions">
                            <div class="fi-ta-col-wrp">
                                <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                    <div class="fi-ta-text grid gap-y-1 px-3 py-4 font-bold">
                                        {{ array_key_first($permissionGroup) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @foreach($permissionGroup as $permission)
                        @foreach($permission as $perm)
                            <tr class="fi-ta-row [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                                <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-actions">
                                    <div class="fi-ta-col-wrp">
                                        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                            <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                {{ $perm  }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                {{--Begin checkboxes loop--}}
                                @foreach($this->getRoles() as $role)
                                    <td class="fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-actions">
                                        <div class="fi-ta-col-wrp">
                                            <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                                <div class="fi-ta-text grid gap-y-1 px-3 py-4">
                                                    <input wire:model="permissions" type="checkbox" value="{{ $role . ":" . $perm }}" aria-label="{{ $role . " can " . $perm }}">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                                {{--End checkboxes loop--}}
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <nav class="block mt-6 text-right">
        <x-filament::button wire:click="updateRoles" tooltip="Update roles and permissions">
            Edit permissions <x-slot name="badge">3</x-slot>
        </x-filament::button>
    </nav>
</div>
