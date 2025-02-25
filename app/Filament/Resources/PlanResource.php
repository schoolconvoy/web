<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscription Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price_monthly')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\TextInput::make('price_yearly')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        Forms\Components\TextInput::make('trial_days')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('days'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Limits')
                    ->schema([
                        Forms\Components\TextInput::make('max_schools')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('max_students')
                            ->required()
                            ->numeric()
                            ->default(100),
                        Forms\Components\TextInput::make('max_teachers')
                            ->required()
                            ->numeric()
                            ->default(10),
                        Forms\Components\TextInput::make('max_parents')
                            ->required()
                            ->numeric()
                            ->default(200),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Payment Gateway Integration')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_monthly_plan_id')
                            ->maxLength(255)
                            ->placeholder('Stripe Monthly Plan ID'),
                        Forms\Components\TextInput::make('stripe_yearly_plan_id')
                            ->maxLength(255)
                            ->placeholder('Stripe Yearly Plan ID'),
                        Forms\Components\TextInput::make('paystack_monthly_plan_id')
                            ->maxLength(255)
                            ->placeholder('Paystack Monthly Plan ID'),
                        Forms\Components\TextInput::make('paystack_yearly_plan_id')
                            ->maxLength(255)
                            ->placeholder('Paystack Yearly Plan ID'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Features')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->schema([
                                Forms\Components\TextInput::make('feature')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->defaultItems(3)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['feature'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_yearly')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_days')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days'),
                Tables\Columns\TextColumn::make('max_schools')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Plans')
                    ->trueLabel('Active Plans')
                    ->falseLabel('Inactive Plans'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            RelationManagers\SubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
