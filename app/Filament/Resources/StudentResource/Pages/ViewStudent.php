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
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Contracts\View\View;
use Filament\Tables\Table;

class ViewStudent extends ViewRecord implements HasTable
{
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.pages.student.pages.view-student';

    use InteractsWithTable;

    protected function getHeaderActions(): array
    {
        return [
            //
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
        return Fee::query()
            ->join('discount_student_fee', 'fees.id', '=', 'discount_student_fee.fee_id')
            ->leftJoin('discounts', 'discount_student_fee.discount_id', '=', 'discounts.id')
            ->leftJoin('fee_categories', 'fees.fee_category', '=', 'fee_categories.id')
            ->where('discount_student_fee.student_id', $this->record->id)
            ->select(
                'fees.id',
                'fees.name as fee_name',
                'fees.amount',
                'fee_categories.name as category',
                'discount_student_fee.discount_id as discount_id',
                'discounts.title as discount_title',
                'discounts.percentage as discount_percentage'
            );
    }

    /**
     * Define which columns to display in the table.
     */
    protected function getTableColumns(): array
    {
        $student = $this->record;

        return [
            TextColumn::make('fee_name')
                ->searchable()
                ->sortable(),
            TextColumn::make('category')
                ->label('Fee Category')
                ->searchable()
                ->sortable(),
            TextColumn::make('discount_percentage')
                ->label('Discount (%)')
                ->description(fn (Fee $fee): string => 'NGN ' . number_format($fee->getTotal($fee->discount_percentage), 2))
                ->formatStateUsing(fn ($state) => ($state ?? 0) . '%'),
            TextColumn::make('amount')
                ->label('Amount')
                ->numeric(2)
                ->money('NGN')
                ->summarize([
                    Sum::make()
                        ->label('Total fees')
                        ->numeric(2)
                        ->money('NGN'),
                    Summarizer::make()
                        ->label('Discounted fees')
                        ->using(
                            function (Table $table) {
                                return $table->getRecords()->sum(
                                    function (Fee $fee) {
                                        return $fee->getTotal($fee->discount_percentage);
                                    }
                                );
                            }
                        )
                        ->numeric(2)
                        ->money('NGN'),
                    Summarizer::make()
                        ->hidden(function () use ($student) {
                            return $student->scholarships()->count() === 0;
                        })
                        ->label('Scholarship total (' . $student->scholarships()->count() . ')')
                        ->using(
                            function () use ($student) {
                                return $student->scholarships()->sum('amount');
                            }
                        )
                        ->prefix('– ')
                        ->numeric(2)
                        ->money('NGN'),
                    Summarizer::make()
                        ->label('FINAL AMOUNT')
                        ->using(
                            function (Table $table) use ($student) {
                                $discountedTotal = $table->getRecords()->sum(
                                    function (Fee $fee) {
                                        return $fee->getTotal($fee->discount_percentage);
                                    }
                                );

                                $scholarshipTotal = $student->scholarships()
                                    ->where(function($query) {
                                        $query->whereNull('end_date')
                                            ->orWhere('end_date', '>=', now());
                                    })
                                    ->sum('amount');

                                return max(0, $discountedTotal - $scholarshipTotal);
                            }
                        )
                        ->numeric(2)
                        ->money('NGN')
                ]),
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

    public function getTables(): array
    {
        return [
            'fees' => $this->feesTable(),
            'finalAmount' => $this->finalAmountTable(),
        ];
    }

    public function feesTable(): Table
    {
        return Table::make($this)
            ->query($this->getTableQuery())
            ->heading('Student Fees')
            ->columns([
                TextColumn::make('fee_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Fee Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_percentage')
                    ->label('Discount (%)')
                    ->description(fn (Fee $fee): string => 'NGN ' . number_format($fee->getTotal($fee->discount_percentage), 2))
                    ->formatStateUsing(fn ($state) => ($state ?? 0) . '%'),
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
                                function (Table $table) {
                                    return $table->getRecords()->sum(
                                        function (Fee $fee) {
                                            return $fee->getTotal($fee->discount_percentage);
                                        }
                                    );
                                }
                            )
                            ->numeric(2)
                            ->money('NGN'),
                        Summarizer::make()
                            ->label('Final amount')
                            ->using(
                                function (User $record) {
                                    return $record->scholarships()->sum('amount');
                                }
                            )
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.fees.edit', $record))
                    ->label('Edit fee'),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->discount_id)
                    ->url(fn ($record) => route('filament.admin.resources.discounts.edit', $record->discount_id))
                    ->label('Edit discount'),
            ]);
    }
}
