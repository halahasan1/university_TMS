<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\HeaderMiniCalendarWidget::class,
            AccountWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return ['sm' => 1, 'lg' => 2, 'xl' => 2];

    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\AdminKpiStats::class,
            \App\Filament\Widgets\TasksCreatedCompletedChart::class,
            \App\Filament\Widgets\TasksByStatusChart::class,
            \App\Filament\Widgets\NewsPerDepartmentPie::class,
            \App\Filament\Widgets\OverdueTasksTable::class,
            \App\Filament\Widgets\UpcomingDeadlinesTable::class,
            \App\Filament\Widgets\ActivityTimelineWidget::class,
            \App\Filament\Widgets\WeeklyCalendarWidget::class,
            \App\Filament\Widgets\NotificationsTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return ['sm' => 1, 'lg' => 2, 'xl' => 2];
    }
}
