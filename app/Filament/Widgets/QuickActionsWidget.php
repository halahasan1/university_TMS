<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static ?string $heading = 'Quick Actions';
    protected static string $view = 'filament.widgets.quick-actions';
    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 1, 'xl' => 1];

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
