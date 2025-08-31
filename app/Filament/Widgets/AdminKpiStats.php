<?php

namespace App\Filament\Widgets;

use App\Models\News;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class AdminKpiStats extends BaseWidget
{
    protected  ?string $heading = 'Overview';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getCards(): array
    {
        $now = now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek   = $now->copy()->endOfWeek();

        $totalTasks  = Task::count();
        $overdue     = Task::where('status', '!=', 'completed')
                        ->whereNotNull('due_date')
                        ->where('due_date', '<', $now)->count();
        $dueThisWeek = Task::whereBetween('due_date', [$startOfWeek, $endOfWeek])->count();

        $totalNews   = News::count();
        $newsThisWk  = News::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();

        return [
            Card::make('Total Tasks', $totalTasks)->icon('heroicon-o-queue-list'),
            Card::make('Overdue', $overdue)->icon('heroicon-o-exclamation-triangle')->color('danger'),
            Card::make('Due this week', $dueThisWeek)->icon('heroicon-o-calendar-days')->color('warning'),

            Card::make('Total News', $totalNews)->icon('heroicon-o-newspaper'),
            Card::make('News this week', $newsThisWk)->icon('heroicon-o-bell')->color('info'),

            Card::make('Active Users', User::count())->icon('heroicon-o-user-group'),
            Card::make('Departments', Department::count())->icon('heroicon-o-building-library'),
        ];
    }
}
