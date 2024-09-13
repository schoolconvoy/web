<?php

namespace App\Livewire\Result;

use App\Models\Result;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;

class ViewResult extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $record;

    public function table(Table $table): Table
    {
        $query = Result::where('term_id', $this->record->term_id)
                        ->where('session_id', $this->record->session_id)
                        ->where('class_id', $this->record->class_id)
                        ->where('subject_id', $this->record->subject_id);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('student.fullname')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ca_1')
                    ->label('CA 1')
                    ->numeric(),
                Tables\Columns\TextColumn::make('ca_2')
                    ->label('CA 2')
                    ->numeric(),
                Tables\Columns\TextColumn::make('exam_score')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('total_score')
                    ->numeric(),
                Tables\Columns\TextColumn::make('grade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remark')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('class.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('session.year')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('term.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                // 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.result.view-result');
    }
}
