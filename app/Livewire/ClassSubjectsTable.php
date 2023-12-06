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
use App\Models\Subject;
use Filament\Notifications\Notification;

class ClassSubjectsTable extends IRelationalEntityTable
{
    public $classId;
    public $students = [];
    public $student = null;
    public $viewPath = 'livewire.class-subjects-table';
    public $subjects;
    public $teacher;
    public $subject; // current subject being assigned a teacher

    protected function getForms(): array
    {
        return [
            'form',
            'teacherForm',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Classes::find($this->classId)->level->subjects()->getQuery())
            ->modelLabel('Subjects')
            ->columns([
                TextColumn::make('name')
                    ->label('Subject')
                    ->searchable(),
                TextColumn::make('teacher')
                    ->description(function(Subject $subject) {
                        return $subject->teacher ? User::find($subject->teacher)->fullname : 'Not set';
                    })
            ])
            ->filters([

            ])
            ->actions([
                ActionGroup::make([
                        TableAction::make('change_teacher')
                                ->icon('heroicon-s-users')
                                ->action(function (Subject $subject) {
                                    $this->subject = $subject->id;
                                    $this->dispatch('open-modal', id: 'add-teacher', subject: $subject->id);
                                }),
                        TableAction::make('remove_subject')
                                ->icon('heroicon-s-minus-circle')
                                ->requiresConfirmation()
                                ->color('danger')
                                ->action(function (Subject $subject) {
                                    $this->removeSubject($subject->id);
                                })
                    ])
            ])
            ->headerActions([
                TableAction::make('add_subjects')
                        ->label('Add subjects')
                        ->icon('heroicon-o-plus-circle')
                        ->color('primary')
                        ->action(function () {
                            $this->dispatch('open-modal', id: 'add-subjects');
                        }),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1
                ])
                ->schema([
                    Select::make('subjects')
                        ->options(Subject::unassigned(Classes::find($this->classId)->level->id)->pluck('name', 'id'))
                        ->multiple()
                        ->required()
                        ->searchable(),
                ])
            ]);
    }

    public function teacherForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1
                ])
                ->schema([
                    Select::make('teacher')
                        ->options(User::role(User::$TEACHER_ROLE)->pluck('firstname', 'id'))
                        ->required()
                        ->searchable(),
                ])
            ]);
    }

    public function assignSubjects()
    {
        $didAttach = Classes::find($this->classId)->level->subjects()->attach($this->subjects);

        if ($didAttach)
        {
            Notification::make()
                ->success()
                ->title('Subjects assigned to class')
                ->body('The selected subjects were assigned successfully.')
                ->send();

            $this->dispatch('close-modal', id: 'add-subjects');
        }
        else
        {
            Notification::make()
                ->danger()
                ->title('Failed to assigned subjects to class')
                ->body('The selected subjects were not assigned.')
                ->send();
        }

    }

    public function assignTeacher()
    {
        $level = Classes::find($this->classId)->level;

        $didUpdate = $level->subjects()->updateExistingPivot($this->subject, [
            'teacher' => $this->teacher
        ]);

        if ($didUpdate)
        {
            Notification::make()
                ->success()
                ->title('Teacher has been assigned to subject successfully')
                ->body('The selected teacher has been assigned to the subject successfully.')
                ->send();

            $this->dispatch('close-modal', id: 'add-teacher');
        }
        else
        {
            Notification::make()
                ->danger()
                ->title('Failed to assign teacher to subject')
                ->body('The selected teacher was not assigned to subject.')
                ->send();
        }
    }

    public function removeSubject($subjectId)
    {
        $didDetach = Classes::find($this->classId)->level->subjects()->detach($subjectId);

        if ($didDetach)
        {
            Notification::make()
                ->success()
                ->title('Subject removed from class')
                ->body('Selected subject has been removed successfully')
                ->send();
        }
    }

    public function mount($classId = null, $students = [])
    {
        $this->classId = $classId;
        $this->students = $students;
    }
}
