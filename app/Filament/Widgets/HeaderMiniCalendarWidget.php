<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\News;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class HeaderMiniCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.header-mini-calendar';
    public static function canView(): bool { return auth()->check(); }
    protected int|string|array $columnSpan = ['sm' => 1, 'lg' => 1, 'xl' => 1];
    protected static ?int $sort = 10;


    protected function getHeading(): string|Htmlable|null
    {
        return 'Mini Calendar';
    }

    protected function getViewData(): array
    {
        $start = now()->startOfWeek();
        $end   = now()->endOfWeek();

        $taskCounts = Task::whereBetween('due_date', [$start, $end])
            ->selectRaw('DATE(due_date) d, COUNT(*) c')
            ->groupBy('d')->pluck('c','d')->all();

        $newsCounts = News::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) d, COUNT(*) c')
            ->groupBy('d')->pluck('c','d')->all();

        $days = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $days[] = [
                'date'     => $cursor->copy(),
                'tasks'    => (int)($taskCounts[$key] ?? 0),
                'news'     => (int)($newsCounts[$key] ?? 0),
                'is_today' => $cursor->isToday(),
            ];
            $cursor->addDay();
        }

        return compact('days');
    }
}
