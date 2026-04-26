<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class ActivityTimelineWidget extends Widget
{
    protected static ?string $heading = 'Recent Activity';
    protected static string $view = 'filament.widgets.activity-timeline';
    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 1, 'xl' => 1];
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getViewData(): array
    {
        $items = Cache::remember('dashboard_activity_timeline', now()->addMinutes(2), function () {
            $tasks = \App\Models\Task::query()
                ->select(['id', 'title', 'updated_at', 'created_by'])
                ->with('createdBy:id,name')
                ->latest('updated_at')
                ->limit(10)
                ->get()
                ->map(fn($t) => [
                    'time' => $t->updated_at,
                    'user' => optional($t->createdBy)->name,
                    'desc' => "Task updated: {$t->title}",
                ]);

            $news = \App\Models\News::query()
                ->select(['id', 'title', 'updated_at', 'user_id'])
                ->with('user:id,name')
                ->latest('updated_at')
                ->limit(10)
                ->get()
                ->map(fn($n) => [
                    'time' => $n->updated_at,
                    'user' => optional($n->user)->name,
                    'desc' => "News updated: {$n->title}",
                ]);

            return $tasks->merge($news)->sortByDesc('time')->take(20)->values()->all();
        });

        return compact('items');
    }
}
