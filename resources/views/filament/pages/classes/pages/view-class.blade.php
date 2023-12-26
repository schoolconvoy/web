<x-filament-panels::page>
    <div>
        <div class="flex flex-row">
            <span class="text-gray-500">{{ $this->record->users()->count() }} students</span>
        </div>
        <div class="flex gap-3 items-center py-3">
            <x-heroicon-m-users class="h-10" />
            <h1 class="text-3xl font-bold text-gray-800">
                {{ $this->record->name }}
            </h1>
        </div>

        <!-- Tab links -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="mr-2" role="presentation">
                    <a href="#students" class="inline-block p-4 border-b-2 rounded-t-lg" id="students-tab" data-tabs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="false">Students</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#attendance" class="inline-block p-4 border-b-2 rounded-t-lg" id="attendance-tab" data-tabs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">Attendance</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#subjects" class="inline-block p-4 border-b-2 rounded-t-lg" id="subjects-tab" data-tabs-target="#subjects" type="button" role="tab" aria-controls="subjects" aria-selected="false">Subjects</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#google-classroom" class="inline-block p-4 border-b-2 rounded-t-lg" id="google-classroom-tab" data-tabs-target="#google-classroom" type="button" role="tab" aria-controls="google-classroom" aria-selected="false">Google Classroom</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#teacher" class="inline-block p-4 border-b-2 rounded-t-lg" id="teacher-tab" data-tabs-target="#teacher" type="button" role="tab" aria-controls="teacher" aria-selected="false">Teacher</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#timetable" class="inline-block p-4 border-b-2 rounded-t-lg" id="timetable-tab" data-tabs-target="#timetable" type="button" role="tab" aria-controls="timetable" aria-selected="false">Timetable</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#reports" class="inline-block p-4 border-b-2 rounded-t-lg" id="reports-tab" data-tabs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">Reports</a>
                </li>
                <li class="mr-2" role="presentation">
                    <a href="#results" class="inline-block p-4 border-b-2 rounded-t-lg" id="results-tab" data-tabs-target="#results" type="button" role="tab" aria-controls="results" aria-selected="false">Results</a>
                </li>
            </ul>
        </div>
        <div id="default-tab-content">
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="students" role="tabpanel" aria-labelledby="students-tab">
                <livewire:class-students-table lazy :students="$this->record->students" :classId="$this->record->id" />
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                <livewire:class-attendance-table lazy :students="$this->record->students" :classId="$this->record->id" />
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
                <livewire:class-subjects-table lazy :students="$this->record->students" :classId="$this->record->id" />
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="google-classroom" role="tabpanel" aria-labelledby="google-classroom-tab">
                <livewire:class-google-classroom lazy :classId="$this->record->id" />
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="teacher" role="tabpanel" aria-labelledby="teacher-tab">
                <livewire:class-teacher-table lazy :classId="$this->record->id" />
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="timetable" role="tabpanel" aria-labelledby="timetable-tab">
                <h2>Timetable</h2>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                <h2>Reports</h2>
            </div>
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="results" role="tabpanel" aria-labelledby="results-tab">
                <h2>Results</h2>
            </div>
        </div>

        @if (count($relationManagers = $this->getRelationManagers()))
            <x-filament-panels::resources.relation-managers
                :active-manager="$activeRelationManager"
                :managers="$relationManagers"
                :owner-record="$record"
                :page-class="static::class"
            />
        @endif
    </div>
</x-filament-panels::page>
