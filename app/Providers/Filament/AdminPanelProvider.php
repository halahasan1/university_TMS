<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Resources\ProfileResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Resources\TaskResource\Pages\MyTasks;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('adminPanel')
            ->path('adminPanel')
            ->login()
            ->routes(function () {
                require base_path('routes/filament/admin.php');
            })
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                MyTasks::class,

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->resources([
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\TaskResource::class,
            ])

            ->navigationItems([
                NavigationItem::make('My Profile')
                    ->url(fn (): string => ProfileResource::getUrl('view', [
                        'record' => auth()->user()->profile->id
                    ]))
                    ->icon('heroicon-o-user-circle')
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.resources.profiles.view')),

                NavigationItem::make('My Tasks')
                    ->url(fn (): string => MyTasks::getUrl())
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (): bool => auth()->user()->hasRole(['student', 'professor', 'dean']))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.pages.my-tasks'))
                    ->sort(3),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
