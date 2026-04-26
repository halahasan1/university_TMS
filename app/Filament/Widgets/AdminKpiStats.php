<?php

namespace App\Filament\Widgets;

use App\Models\News;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;

class AdminKpiStats extends BaseWidget
{
    protected  ?string $heading = 'Overview';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getCards(): array
    {
        $data = Cache::remember('dashboard_admin_kpis', now()->addMinutes(2), function () {
            $now = now();
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek   = $now->copy()->endOfWeek();
    
            return [
                'totalTasks'  => Task::count(),
                'overdue'     => Task::where('status', '!=', 'completed')
                                    ->whereNotNull('due_date')
                                    ->where('due_date', '<', $now)
                                    ->count(),
                'dueThisWeek' => Task::whereBetween('due_date', [$startOfWeek, $endOfWeek])->count(),
    
                'totalNews'   => News::count(),
                'newsThisWk'  => News::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
    
                'users'       => User::count(),
                'depts'       => Department::count(),
            ];
        });
    
        return [
            Card::make('Total Tasks', $data['totalTasks'])->icon('heroicon-o-queue-list'),
            Card::make('Overdue', $data['overdue'])->icon('heroicon-o-exclamation-triangle')->color('danger'),
            Card::make('Due this week', $data['dueThisWeek'])->icon('heroicon-o-calendar-days')->color('warning'),
    
            Card::make('Total News', $data['totalNews'])->icon('heroicon-o-newspaper'),
            Card::make('News this week', $data['newsThisWk'])->icon('heroicon-o-bell')->color('info'),
    
            Card::make('Active Users', $data['users'])->icon('heroicon-o-user-group'),
            Card::make('Departments', $data['depts'])->icon('heroicon-o-building-library'),
        ];
    }
}
