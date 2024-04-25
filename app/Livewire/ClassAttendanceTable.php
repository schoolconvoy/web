<?php

namespace App\Livewire;

use App\Events\StudentIsLate;
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
use App\Models\Attendance;
use App\Models\UserMeta;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\SelectColumn;

class ClassAttendanceTable extends IRelationalEntityTable
{
    public $classId = '';
    public $students = [];
    public $student = null;
    public $viewPath = 'livewire.class-attendance-table';

    public function table(Table $table): Table
    {
        return $table
            ->query(Attendance::initiate($this->classId))
            ->modelLabel('Students')
            ->columns([
                TextColumn::make('students.fullname')
                    ->label('Students')
                    ->searchable()
                    ->description(fn (Attendance $record): string => $record->students->admission_no ?? ''),
                SelectColumn::make('status')
                    ->options([
                        1 => 'Present',
                        2 => 'Absent',
                        3 => 'Late',
                    ])
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Attendance')
                    ->options([
                        1 => 'Present',
                        2 => 'Absent',
                        3 => 'Late'
                    ])
            ])
            ->headerActions([
                // TableAction::make('export')
                //         ->label('Export')
                //         ->icon('heroicon-o-cloud-arrow-up')
                //         ->color('primary')
                //         ->form([
                //             FileUpload::make('Import students')
                //                     ->acceptedFileTypes(['application/pdf'])
                //         ])
                //         ->action(function () {
                //             // TODO: Allow bulk import students to a class
                //             // Handle file upload
                //         }),
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

    public function mount($classId = null, $students = [])
    {
        $this->classId = $classId;
        $this->students = $students;
    }

    public function updated($property)
    {
        // $property: The name of the current property that was updated

        Log::debug('Updating property. .. ' . print_r($property, true));
    }
}
