<?php

namespace App\Livewire;

use App\Models\Classes;
use Livewire\Component;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Table;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\Log;

abstract class IRelationalEntityTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithInfolists;
    use InteractsWithForms;
    use InteractsWithTable;

    public $viewPath;

    public function table(Table $table): Table
    {
        return $table
            ->query(Classes::find($this->classId)->users()->getQuery())
            ->modelLabel('Students')
            ->columns([
                TextColumn::make('fullname')
                    ->label('students')
                    ->searchable()
                    ->description(fn (User $record): string => $record->meta()->where('key', 'admission_no')->first()->value ?? ''),
            ])
            ->filters([

            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                ]),
            ])
            ->headerActions([
                TableAction::make('import')
                        ->label('Bulk Import')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->color('primary')
                        ->form([
                            FileUpload::make('Import students')
                                    ->acceptedFileTypes(['application/pdf'])
                        ])
                        ->action(function () {
                            // TODO: Allow bulk import students to a class
                            // Handle file upload
                        }),
                TableAction::make('add_students')
                        ->label('Add Class Member')
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
                            ->icon('heroicon-m-x-mark')
                            ->color('danger')
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

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <!-- Loading spinner... -->
            <svg>...</svg>
        </div>
        HTML;
    }
    
    public function render()
    {
        Log::debug('Rendering view: ' . $this->viewPath);
        return view($this->viewPath);
    }
}
