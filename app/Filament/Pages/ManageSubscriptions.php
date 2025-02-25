<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\SubscriptionService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Carbon\Carbon;

class ManageSubscriptions extends Page
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscription Management';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.manage-subscriptions';

    public ?Tenant $tenant = null;

    public function mount(): void
    {
        $this->tenant = $this->getCurrentTenant();
    }

    public function getTenantSubscriptionsTable(): Table
    {
        return Tables\Table::make($this)
            ->query(
                $this->tenant
                    ? Subscription::query()->where('tenant_id', $this->tenant->id)
                    : Subscription::query()
            )
            ->columns([
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
                    ->query(fn ($query) => $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now())),
                Tables\Filters\Filter::make('active')
                    ->label('Active Subscriptions')
                    ->query(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', Carbon::now())),
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Subscription')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $subscriptionService = app(SubscriptionService::class);
                        $subscriptionService->cancelSubscription($record);

                        Notification::make()
                            ->title('Subscription cancelled successfully')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->ends_at === null || $record->ends_at->isFuture()),
            ]);
    }

    public function getSubscribeForm(): Form
    {
        return Forms\Form::make($this)
            ->schema([
                Forms\Components\Select::make('plan_id')
                    ->label('Select a Plan')
                    ->options(Plan::active()->get()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Forms\Components\Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'stripe' => 'Stripe',
                        'paystack' => 'Paystack',
                    ])
                    ->required()
                    ->default('stripe'),
                Forms\Components\Select::make('interval')
                    ->label('Billing Interval')
                    ->options([
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required()
                    ->default('monthly'),
            ])
            ->statePath('subscribeData');
    }

    public $subscribeData = [];

    public function subscribe()
    {
        $data = $this->getSubscribeForm()->getState();

        if (!$this->tenant) {
            Notification::make()
                ->title('No tenant selected')
                ->danger()
                ->send();

            return;
        }

        $plan = Plan::find($data['plan_id']);

        if (!$plan) {
            Notification::make()
                ->title('Invalid plan selected')
                ->danger()
                ->send();

            return;
        }

        try {
            $subscriptionService = app(SubscriptionService::class);
            $subscription = $subscriptionService->createSubscription(
                $this->tenant,
                $plan,
                $data['payment_method'],
                $data['interval']
            );

            Notification::make()
                ->title('Successfully subscribed to ' . $plan->name . ' plan')
                ->success()
                ->send();

            $this->reset('subscribeData');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to process subscription')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Get the current tenant for the authenticated user.
     *
     * @return \App\Models\Tenant|null
     */
    protected function getCurrentTenant()
    {
        if (SpatieTenant::checkCurrent()) {
            return Tenant::find(SpatieTenant::current()->id);
        }

        // For super admins or when not in a tenant context
        $user = Auth::user();
        if ($user && $user instanceof User && $user->hasRole(User::$SUPER_ADMIN_ROLE)) {
            // Super admin might be managing subscriptions for a specific tenant
            // You could add logic here to determine which tenant they're managing
            return null;
        }

        return $user ? $user->tenant : null;
    }
}
