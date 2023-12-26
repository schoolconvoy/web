<?php

namespace App\Livewire;

use App\Events\NewClassTeacher;
use App\Models\Classes;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use App\Livewire\IRelationalEntityTable;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Actions\Action as InfoListAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions\Action as ButtonAction;
use Illuminate\Database\Eloquent\Model;

class ClassTeacherTable extends IRelationalEntityTable implements HasInfolists, HasActions
{
    use InteractsWithInfolists;
    use InteractsWithActions;
    use InteractsWithForms;

    public $classId = '';
    public $viewPath = 'livewire.class-teacher-table';
    public $teacher = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(Classes::find($this->classId)->teacher()->getQuery())
            ->modelLabel('Teacher')
            ->columns([
                TextColumn::make('fullname')
                    ->label('Teacher'),
            ])
            ->filters([

            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
                ->record(Classes::find($this->classId)->teacher()->first() ?? new User())
                ->schema([
                    Section::make('Class Teacher')
                    ->description('You can see the assigned teacher or assign a new one')
                    ->icon('heroicon-s-folder-open')
                    ->schema([
                        Infolists\Components\TextEntry::make('fullname')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->default('No teacher assigned'),
                        \Filament\Infolists\Components\Actions::make([
                            InfoListAction::make('assign_new_teacher')
                                ->icon('heroicon-s-user-circle')
                                ->color('primary')
                                ->action(fn() => $this->dispatch('open-modal', id: 'add-teacher'))
                        ]),
                    ]),
                ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('teacher')
                    ->label('Select teacher')
                    ->options(User::role(User::$TEACHER_ROLE)->pluck('firstname', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }

    public function save()
    {
        Log::debug('Save button called from action button');

        $class = Classes::find($this->classId);

        $class->teacher = $this->teacher;
        $class->save();

        NewClassTeacher::dispatch($class);

        // close the modal
        $this->dispatch('close-modal', id: 'add-teacher');
    }

    public function saveButtonAction(): Action
    {
        return Action::make('saveButton')
                    ->label('Complete')
                    ->requiresConfirmation()
                    ->action(function () {
                        Log::debug('Save button called from action button');
                        $this->save();
                    });
    }

    public function mount($classId = null)
    {
        $this->classId = $classId;
    }
}
