<?php

namespace App\Livewire;

use App\Events\StudentPromoted;
use App\Models\Classes;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action as TableAction;
use App\Livewire\IRelationalEntityTable;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use App\Models\Tenant;

class ClassStudentsTable extends IRelationalEntityTable
{
    public $classId = '';
    public $students = [];
    public $student = null;
    public $viewPath = 'livewire.class-students-table';

    public function table(Table $table): Table
    {
        return $table
            ->query(Classes::find($this->classId)->users()->getQuery())
            ->modelLabel('Students')
            ->columns([
                TextColumn::make('firstname')
                    ->label('Students')
                    ->formatStateUsing(fn (User $record): string => __(':firstname :lastname', ['firstname' => $record->firstname, 'lastname' => $record->lastname]))
                    ->searchable()
                    ->description(fn (User $record): string => $record->admission_no ?? ''),
            ])
            ->filters([

            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                                ->url(fn (User $record): string => route('filament.admin.resources.students.view', $record)),
                    EditAction::make()
                                ->url(fn (User $record): string => route('filament.admin.resources.students.edit', $record)),
                    TableAction::make('remove_student')
                                ->icon('heroicon-o-user-minus')
                                ->requiresConfirmation()
                                ->action(function (User $record) {
                                    $record->class_id = null;
                                    $record->save();
                                }),
                    TableAction::make('promote_student')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->requiresConfirmation()
                        ->action(function (User $record) {
                            $nextClassName = $record->promote();

                            Notification::make()
                                ->title('Student promoted successfully')
                                ->body($record->lastname . ' ' . $record->firstname . ' has been promoted to ' . $nextClassName)
                                ->success()
                                ->send();
                        })

                ]),
            ])
            ->headerActions([
                // TableAction::make('import')
                //         ->label('Bulk Import')
                //         ->icon('heroicon-o-cloud-arrow-down')
                //         ->color('primary')
                //         ->form([
                //             FileUpload::make('Import students')
                //                     ->acceptedFileTypes(['application/pdf'])
                //         ])
                //         ->action(function () {
                //             // TODO: Allow bulk import students to a class
                //             // Handle file upload
                //         }),
                TableAction::make('add_students')
                        ->label('Add Class Member')
                        ->icon('heroicon-o-user-plus')
                        ->action(fn() => $this->dispatch('open-modal', id: 'add-students'))
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2
                ])
                ->schema([
                    Select::make('student')
                        ->label('Student')
                        ->options(User::studentsDropdown())
                        ->required()
                        ->searchable(),
                    Actions::make([
                        Action::make('addStudent')
                            ->icon('heroicon-o-user-plus')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(function (Set $set, Get $get, $state) {
                                $this->addItem($this->student);
                                $set('student', '');
                            })
                    ])
                    ->verticalAlignment(VerticalAlignment::End),
                ])
            ]);
    }

    public function addItem($student)
    {
        // Add the selected option to the list of selected items
        if (!empty($student)) {
            $this->students[] = User::find($student);
            $student = null; // Reset the selected option after adding
        }
    }

    public function removeStudent($index)
    {
        // Remove the selected item from the list
        unset($this->students[$index]);
        $this->students = array_values($this->students); // Reindex the array
    }

    public function saveStudents()
    {
        $class = Classes::find($this->classId);
        $tenant = Tenant::find(auth()->user()->school_id);

        if (!$tenant || !$tenant->subscription || !$tenant->subscription->isActive()) {
            Notification::make()
                ->title('Subscription Required')
                ->body('Please subscribe to a plan to add students to classes.')
                ->danger()
                ->send();
            return;
        }

        if ($tenant->exceedsStudentLimit()) {
            Notification::make()
                ->title('Student Limit Reached')
                ->body('You have reached the maximum number of students allowed in your subscription plan.')
                ->danger()
                ->send();
            return;
        }

        foreach($this->students as $student)
        {
            $oldClass = Classes::find($student->class_id) ?? new Classes();
            $student->class_id = $this->classId;
            $student->save();

            StudentPromoted::dispatch($student, $oldClass, $class);
        }

        // close the modal
        $this->dispatch('close-modal', id: 'add-students');

        Notification::make()
            ->title('Students Added')
            ->body('Students have been successfully added to the class.')
            ->success()
            ->send();
    }

    public function mount($classId = null, $students = [])
    {
        $this->classId = $classId;
        $this->students = $students;
    }
}
