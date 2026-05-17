<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CourseResource\Pages\MyCourses;
use App\Filament\Resources\ExamResource\Pages\MyExams;
use App\Filament\Resources\ExamResource\Pages\TakeExam;
use App\Filament\Resources\ProfileResource;
use App\Filament\Resources\TaskResource\Pages\MyTasks;
use App\Filament\Widgets\HeaderMiniCalendarWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
                MyTasks::class,
                MyCourses::class,
                MyExams::class,
                TakeExam::class
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

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
                // NavigationItem::make('My Courses')
                    // ->url(fn (): string => MyCourses::getUrl())
                    // ->icon('heroicon-o-academic-cap')
                    // ->visible(fn (): bool => auth()->user()->hasRole('student'))
                    // ->isActiveWhen(fn (): bool => request()->url() === \App\Filament\Resources\CourseResource\Pages\MyCourses::getUrl())
                    ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
