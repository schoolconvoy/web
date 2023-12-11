<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeResource\Pages;
use App\Filament\Resources\FeeResource\RelationManagers;
use App\Filament\Resources\FeeResource\Widgets\WardFees;
use App\Models\Classes;
use App\Models\Fee;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Cache;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                            ->required(),
                Select::make('fee_category')
                        ->label('Fee category')
                        ->helperText('Select a category this fee belongs to or create a new one.')
                        ->relationship('category', 'name')
                        ->createOptionForm([
                            TextInput::make('name')
                                        ->required(),
                            TextInput::make('description')
                        ])
                        ->required(),
                TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->prefix('₦'),
                Textarea::make('description')
                            ->autosize(),
                DatePicker::make('deadline'),
                Select::make('classes')
                            ->relationship('classes', 'name')
                            ->options(Classes::all()->pluck('name', 'id'))
                            ->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                if (auth()->user()->hasRole(User::$PARENT_ROLE))
                {
                    $ward = Cache::get('ward', 0);

                    return User::find($ward)->class->fees()->whereDoesntHave('payments') ?? Fee::where('id', 0);
                }
                else
                {
                    return Fee::query();
                }
            })
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('amount')
                            ->numeric(2)
                            ->money('NGN')
                            ->summarize(Summarizer::make()
                            ->label('Total')
                            ->using(fn (QueryBuilder $query): string => $query->sum('amount'))->money('NGN')),
                TextColumn::make('category.name'),

            ])
            // Group summary is wrong at the moment
            ->defaultGroup(
                'category.name',
            )
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('pay_with_Paystack')
                                        ->action(function () {
                                            return redirect(route('pay'));
                                        })
            ])
            // ->actions([
            //     Tables\Actions\Action::make('pay')
            // ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         BulkAction::make('pay')
            //                     ->requiresConfirmation()
            //                     ->action(function() {

            //                     })
            //         ,
            //     ]),
            // ])
            ;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(User::$PARENT_ROLE);
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->hasRole(User::$PARENT_ROLE) || auth()->user()->hasRole(User::$SUPER_ADMIN_ROLE), 403);
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
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'view' => Pages\ViewFee::route('/{record}'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}
