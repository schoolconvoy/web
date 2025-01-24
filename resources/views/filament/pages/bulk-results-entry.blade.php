<x-filament-panels::page>
    <div class="flex gap-4 mb-6">
        <x-filament::input.wrapper label="Session">
            <x-filament::input.select wire:model="sessionId">
                <option value="">Select Session</option>
                @foreach ($sessions as $session)
                    <option value="{{ $session->id }}">{{ $session->year }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper label="Term">
            <x-filament::input.select wire:model="termId">
                <option value="">Select Term</option>
                @foreach ($terms as $term)
                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper label="Class">
            <x-filament::input.select wire:model="classId">
                <option value="">Select Class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper label="Subject">
            <x-filament::input.select wire:model="subjectId">
                <option value="">Select Subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::button wire:click="filterStudents">
            Filter
        </x-filament::button>
    </div>

    <form method="POST">
        <!-- Table for students -->
        <div class="bg-white mt-6 overflow-hidden p-6 rounded-xl shadow sm:rounded-lg">
            <table class="mt-6 w-full">
                <thead>
                    <tr>
                        <th class="text-left">Student</th>
                        <th class="text-left">CA</th>
                        <th class="text-left">Exam</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->filteredStudents as $student)
                        <tr>
                            <td class="py-2">
                                {{ $student->firstname }} {{ $student->lastname }}
                            </td>
                            <td class="py-2 pr-2">
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="number" step="any"
                                        wire:model="results.{{ $student->id }}.ca1"
                                    />
                                </x-filament::input.wrapper>
                            </td>
                            <td class="py-2 pr-2">
                                <x-filament::input.wrapper>
                                    <x-filament::input
                                        type="number" step="any"
                                        wire:model="results.{{ $student->id }}.exam_score"
                                    />
                                </x-filament::input.wrapper>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-2 text-center text-gray-500">No students found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                <x-filament::button wire:click="saveBulkResults">
                    Save All
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
