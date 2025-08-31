<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ActivityTimelineWidget extends Widget
{
    protected static ?string $heading = 'Recent Activity';
    protected static string $view = 'filament.widgets.activity-timeline';
    protected int|string|array $columnSpan = ['sm' => 'full', 'lg' => 1, 'xl' => 1];

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getViewData(): array
    {
        $tasks = \App\Models\Task::latest()->limit(10)->get()->map(fn($t)=>[
            'time'=>$t->updated_at,'user'=>optional($t->createdBy)->name,'desc'=>"Task updated: {$t->title}"
        ]);
        $news  = \App\Models\News::latest()->limit(10)->get()->map(fn($n)=>[
            'time'=>$n->updated_at,'user'=>optional($n->user)->name,'desc'=>"News updated: {$n->title}"
        ]);
        $items = $tasks->merge($news)->sortByDesc('time')->take(20)->values()->all();

        return compact('items');
    }
}
