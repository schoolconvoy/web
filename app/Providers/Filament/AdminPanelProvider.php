<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Register;
use App\Filament\Resources\StaffResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\ParentDashboard;
use App\Filament\Resources\FeeResource\Widgets\IncomeChart;
use App\Filament\Resources\FeeResource\Widgets\IncomeStatsOverview;
use App\Filament\Student\Resources\StudentResource\Widgets\PopulationStatsOverview;
use App\Filament\Widgets\AttendanceOverview;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                IncomeStatsOverview::class,
                AttendanceOverview::class,
                PopulationStatsOverview::class,
                IncomeChart::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                'role:Admin|super-admin|Teacher|Principal|Accountant|Librarian|Receptionist'
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Staff')
                    ->url(fn (): string => StaffResource::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            // ->navigationGroups([
            //     NavigationGroup::make()
            //         ->label('Settings')
            // ])
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->sidebarCollapsibleOnDesktop();
    }
}
