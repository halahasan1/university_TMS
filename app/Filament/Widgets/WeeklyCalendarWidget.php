<?php

namespace App\Filament\Widgets;

use App\Models\News;
use App\Models\Task;
use Filament\Widgets\Widget;

class WeeklyCalendarWidget extends Widget
{
    protected static ?string $heading = 'This Week';
    protected static string $view = 'filament.widgets.weekly-calendar';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getViewData(): array
    {
        $start = now()->startOfWeek();
        $end   = now()->endOfWeek();

        $tasks = Task::whereBetween('due_date', [$start, $end])
            ->select('id','title','due_date','assigned_to','status')
            ->with('assignedTo:id,name')->get();

        $news = News::whereBetween('created_at', [$start, $end])
            ->select('id','title','created_at','user_id')
            ->with('user:id,name')->get();

        $days=[];$cursor=$start->copy();
        while($cursor->lte($end)){
            $k=$cursor->toDateString();
            $days[$k]=['date'=>$cursor->copy(),'tasks'=>[],'news'=>[]];
            $cursor->addDay();
        }
        foreach($tasks as $t){ $days[$t->due_date->toDateString()]['tasks'][]=$t ?? null; }
        foreach($news as $n){ $days[$n->created_at->toDateString()]['news'][]=$n ?? null; }

        return ['days' => $days];
    }
}
