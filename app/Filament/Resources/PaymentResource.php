<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Widgets\IncomeChart;
use App\Filament\Resources\FeeResource\Widgets\IncomeStatsOverview;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // You can't create a manual payment since only online payment is accepted
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fees.name')
                    ->label('Fee(s)')
                    ->listWithLineBreaks()
                    ->bulleted(),
                TextColumn::make('amount')
                    ->numeric()
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('student.firstname')
                    ->label('Paid by')
                    ->formatStateUsing(fn (Model $record): string => "{$record->student->firstname} {$record->student->lastname} ({$record->student->admission_no})")
                    ->description(fn (Payment $record): string => $record->student->class?->name ?? 'No Class')
                    ->searchable(['student.firstname', 'student.lastname', 'student.admission_no']),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'card' => 'success',
                        'cash' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('fees.category.name')
                    ->label('Fee Categories')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->sortable(),
                TextColumn::make('provider')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Payment Date')
                    ->dateTime('D, M j, Y, g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->relationship('fees.category', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('type')
                    ->options([
                        'card' => 'Card',
                        'cash' => 'Cash'
                    ]),
                SelectFilter::make('provider')
                    ->options([
                        'paystack' => 'Paystack',
                        'flutterwave' => 'Flutterwave',
                        'cash' => 'Cash'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('view_student')
                    ->label('View student fees')
                    ->url(fn (Payment $record): string => route('filament.admin.resources.students.view', ['record' => $record->student_id]) . '?tab=fees')
                    ->icon('heroicon-m-academic-cap'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payment Details')
                    ->schema([
                        TextEntry::make('amount')
                            ->money('NGN')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'card' => 'success',
                                'cash' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('provider')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label('Payment Date')
                            ->dateTime('D, M j, Y, g:i A'),
                    ])->columns(2),

                Section::make('Student Information')
                    ->schema([
                        TextEntry::make('student.firstname')
                            ->label('Student Name')
                            ->formatState(fn (Model $record): string => "{$record->student->firstname} {$record->student->lastname}")
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('student.admission_no')
                            ->label('Admission Number'),
                        TextEntry::make('student.class.name')
                            ->label('Class'),
                    ])->columns(2),

                Section::make('Fees Paid')
                    ->schema([
                        TextEntry::make('fees.name')
                            ->label('Fees')
                            ->listWithLineBreaks()
                            ->bulleted(),
                        TextEntry::make('fees.category.name')
                            ->label('Fee Categories')
                            ->listWithLineBreaks()
                            ->bulleted(),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            IncomeChart::class,
            IncomeStatsOverview::class
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
