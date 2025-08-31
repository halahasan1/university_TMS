<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static ?string $heading = null; 
    protected static string $view = 'filament.widgets.welcome-widget';

    protected int|string|array $columnSpan = ['sm' => 1, 'lg' => 1, 'xl' => 1];

    protected static ?int $sort = 11;

    public static function canView(): bool
    {
        return auth()->check();
    }
}
