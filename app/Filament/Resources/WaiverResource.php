<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaiverResource\Pages;
use App\Models\Waiver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;

class WaiverResource extends Resource
{
    protected static ?string $model = Waiver::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('student_id')
                    ->label('Student')
                    ->relationship(
                        'student',
                        'firstname',
                        fn ($query) => $query->role(User::$STUDENT_ROLE)
                            ->with('class')
                            ->orderBy('firstname')
                    )
                    ->getOptionLabelFromRecordUsing(function (Model $record) {
                        $className = $record->class ? $record->class->name : 'No Class';
                        return "{$record->firstname} {$record->lastname} ({$record->admission_no}) - {$className}";
                    })
                    ->searchable(['firstname', 'lastname', 'admission_no'])
                    ->preload()
                    ->required()
                    ->helperText('Search by name or admission number'),
                Select::make('fees')
                    ->relationship(
                        'fees',
                        'name',
                        fn ($query) => $query->with('category')->orderBy('fee_category')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - NGN " . number_format($record->amount, 2) . " ({$record->category->name})")
                    ->multiple()
                    ->preload()
                    ->required()
                    ->searchable()
                    ->helperText('Select the fees that should be waived. The amount shown is the full fee amount before any discounts.')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->maxLength(500),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->helperText('Leave empty if waiver has no end date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.firstname')
                    ->label('Student')
                    ->formatStateUsing(fn (Model $record): string => "{$record->student->firstname} {$record->student->lastname}")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fees.name')
                    ->label('Waived Fees')
                    ->listWithLineBreaks()
                    ->bulleted(),
                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                    ->label('View student fees')
                    ->url(fn (Waiver $record): string => route('filament.admin.resources.students.view', ['record' => $record->student_id]) . '?tab=fees'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListWaivers::route('/'),
            'create' => Pages\CreateWaiver::route('/create'),
            'edit' => Pages\EditWaiver::route('/{record}/edit'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->fullname;
        return $data;
    }
}
