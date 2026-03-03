<?php

namespace App\Providers\Filament;

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
use Filament\FontProviders\LocalFontProvider;
use App\Http\Middleware\ProfileCompleted;
use Filament\Navigation\MenuItem;
use App\Filament\Dealer\Pages\EditProfile;
use Illuminate\Contracts\View\View;
use App\Filament\Dealer\Pages\Auth\Login;
use App\Filament\Dealer\Pages\Auth\Register;

class DealerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dealer')
            ->path('/')
            ->colors([
                'primary' => '#fed526',
            ])
            ->font(
                'Inter',
                url: asset('css/font.css'),
                provider: LocalFontProvider::class,
            )
            ->viteTheme('resources/css/filament/dealer/theme.css')
            ->favicon(asset('favicon.ico'))
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2em')
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Dealer/Resources'), for: 'App\\Filament\\Dealer\\Resources')
            ->discoverPages(in: app_path('Filament/Dealer/Pages'), for: 'App\\Filament\\Dealer\\Pages')
            ->pages([
                //Pages\Dashboard::class,
            ])
            ->profile()
            ->spa()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->url(fn (): string => EditProfile::getUrl())
                    ->label(fn (): string => EditProfile::getnavigationLabel()),
            ])
            ->discoverWidgets(in: app_path('Filament/Dealer/Widgets'), for: 'App\\Filament\\Dealer\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
                ProfileCompleted::class,
            ])
            ->maxContentWidth('screen-lg')
            ->databaseNotifications()
            ->login(Login::class)
            ->registration(Register::class)
            ->renderHook(
                'panels::footer',
                fn (): View => view('filament/common/footer'),
            )
            ->passwordReset()
            ->emailVerification()
            ->topNavigation()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
