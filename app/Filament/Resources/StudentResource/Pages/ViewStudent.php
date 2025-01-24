<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use App\Models\Fee;
use Filament\Forms\Components\FileUpload;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewStudent extends ViewRecord implements HasTable
{
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.pages.student.pages.view-student';

    use InteractsWithTable;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    /**
     * Define the Eloquent query that your table will use.
     * For example, show fees that belong to this student.
     */
    protected function getTableQuery()
    {
        // $this->record is the current student record (since we're on "ViewRecord")
        return Fee::query()
                     ->whereHas('students', fn ($q) => $q->where('student_id', $this->record->id));
    }

    /**
     * Define which columns to display in the table.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name'),
            TextColumn::make('category.name')->label('Fee Category'),
            TextColumn::make('amount')->label('Amount')->numeric(2)->money('NGN'),
            // etc...
        ];
    }

    /**
     * (Optional) Define any table actions, filters, pagination, etc.
     */
    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                            ->url(fn ($record) => route('filament.admin.resources.fees.edit', $record)),
        ];
    }


}
