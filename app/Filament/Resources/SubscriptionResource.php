<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Subscription Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->options(Tenant::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->options(Plan::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Payment Gateway Details')
                    ->schema([
                        Forms\Components\Tabs::make('Payment Gateways')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('Stripe')
                                    ->schema([
                                        Forms\Components\TextInput::make('stripe_id')
                                            ->label('Stripe ID')
                                            ->maxLength(255),
                                        Forms\Components\Select::make('stripe_status')
                                            ->label('Status')
                                            ->options([
                                                'active' => 'Active',
                                                'canceled' => 'Canceled',
                                                'past_due' => 'Past Due',
                                                'unpaid' => 'Unpaid',
                                                'incomplete' => 'Incomplete',
                                                'incomplete_expired' => 'Incomplete Expired',
                                            ]),
                                        Forms\Components\TextInput::make('stripe_price')
                                            ->label('Stripe Price ID')
                                            ->maxLength(255),
                                    ])
                                    ->columns(3),

                                Forms\Components\Tabs\Tab::make('Paystack')
                                    ->schema([
                                        Forms\Components\TextInput::make('paystack_id')
                                            ->label('Paystack ID')
                                            ->maxLength(255),
                                        Forms\Components\Select::make('paystack_status')
                                            ->label('Status')
                                            ->options([
                                                'active' => 'Active',
                                                'canceled' => 'Canceled',
                                                'past_due' => 'Past Due',
                                                'unpaid' => 'Unpaid',
                                            ]),
                                        Forms\Components\TextInput::make('paystack_plan')
                                            ->label('Paystack Plan ID')
                                            ->maxLength(255),
                                    ])
                                    ->columns(3),
                            ]),
                    ]),

                Forms\Components\Section::make('Subscription Period')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At'),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Subscription Ends At'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'canceled' => 'danger',
                        'past_due' => 'warning',
                        'unpaid' => 'danger',
                        'incomplete' => 'warning',
                        'incomplete_expired' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->trial_ends_at && $record->trial_ends_at->isFuture() ? 'info' : 'gray')
                    ->label('Trial Ends'),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->ends_at && $record->ends_at->isFuture() ? 'warning' : 'danger')
                    ->label('Subscription Ends'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->options(Tenant::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('stripe_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'canceled' => 'Canceled',
                        'past_due' => 'Past Due',
                        'unpaid' => 'Unpaid',
                        'incomplete' => 'Incomplete',
                        'incomplete_expired' => 'Incomplete Expired',
                    ]),
                Tables\Filters\Filter::make('trial')
                    ->label('On Trial')
                    ->query(fn (Builder $query) => $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now())),
                Tables\Filters\Filter::make('active')
                    ->label('Active Subscriptions')
                    ->query(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', Carbon::now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Subscription')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $subscriptionService = app(SubscriptionService::class);
                        $subscriptionService->cancelSubscription($record);
                    })
                    ->visible(fn ($record) => $record->ends_at === null || $record->ends_at->isFuture()),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('ends_at')->orWhere('ends_at', '>', Carbon::now())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
