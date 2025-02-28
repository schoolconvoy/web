<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Tenant;
use App\Models\Plan;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;

class ManageSubscription extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.manage-subscription';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $title = 'Subscription Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'manage-subscription';

    public ?array $data = [];

    public $plan_id;

    public function getSubheading(): ?string
    {
        return 'Manage your subscription plan and billing settings';
    }

    public function mount(): void
    {
        $tenant = Tenant::find(Auth::user()->school_id);
        $this->plan_id = $tenant->plan_id ?? null;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Current Subscription')
                    ->description('Manage your subscription plan and billing')
                    ->schema([
                        Select::make('plan_id')
                            ->label('Subscription Plan')
                            ->options(Plan::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    $this->updatePlan($state);
                                }
                            }),
                    ]),
            ]);
    }

    protected function updatePlan($planId): void
    {
        $tenant = Tenant::find(Auth::user()->school_id);
        $plan = Plan::find($planId);

        if (!$tenant || !$plan) {
            return;
        }

        try {
            $tenant->plan_id = $planId;
            $tenant->save();

            Notification::make()
                ->title('Plan Updated')
                ->body('Your subscription plan has been updated successfully.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('There was an error updating your subscription plan.')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
