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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\Summarizers\Sum;
// use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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

    public function getTitle(): string
    {
        return "Viewing: " . $this->record->firstname . ' ' . $this->record->lastname;
    }

    /**
     * Define the Eloquent query that your table will use.
     * For example, show fees that belong to this student.
     */
    protected function getTableQuery(): Builder
    {
        return $this->record->fees()
                            // Since the relationship already uses the pivot table, we join discounts with a left join.
                            ->leftJoin('discounts', 'discount_student_fee.discount_id', '=', 'discounts.id')
                            ->leftJoin('fee_categories', 'fees.fee_category', '=', 'fee_categories.id')
                            ->select(
                                'fees.id',
                                'fees.name as fee_name',
                                'fees.amount',
                                'fee_categories.name as category',
                                'discount_student_fee.discount_id as discount_id',
                                'discounts.title as discount_title',
                                'discounts.percentage as discount_percentage'
                            )
                            ->getQuery();
    }

    /**
     * Define which columns to display in the table.
     */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('fee_name'),
            TextColumn::make('category')->label('Fee Category'),
            TextColumn::make('discount_percentage')
                ->label('Discount (%)')
                ->description(fn (Fee $fee): string => 'NGN ' . number_format($fee->getTotal($fee->discount_percentage), 2))
                ->formatStateUsing(fn ($state) => $state . '%'),
            TextColumn::make('amount')
                ->label('Amount')
                ->numeric(2)
                ->money('NGN')
                ->summarize([
                    Sum::make()
                        ->label('Total amount')
                        ->numeric(2)
                        ->money('NGN'),
                    Summarizer::make()
                        ->label('Discounted amount')
                        ->using(
                            function () {
                                return $this->table->getRecords()->sum(
                                    function (Fee $fee) {
                                        return $fee->getTotal($fee->discount_percentage);
                                    }
                                );
                            }
                        )
                        ->numeric(2)
                        ->money('NGN'),
                ]),
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
                            ->url(fn ($record) => route('filament.admin.resources.fees.edit', $record))
                            ->label('Edit fee'),
            Tables\Actions\EditAction::make()
                            ->visible(fn ($record) => $record->discount_id)
                            ->url(fn ($record) => route('filament.admin.resources.discounts.edit', $record->discount_id))
                            ->label('Edit discount'),
        ];
    }


}
