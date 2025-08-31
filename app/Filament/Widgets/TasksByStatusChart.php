<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TasksByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Tasks by Status';
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getData(): array
    {
        $rows = Task::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->orderBy('status')->pluck('total','status');

        $labels = $rows->keys()->map(fn($s)=>str($s)->replace('_',' ')->headline())->all();

        return [
            'datasets' => [['label' => 'Tasks', 'data' => $rows->values()->all()]],
            'labels'   => $labels,
        ];
    }

    protected function getType(): string { return 'bar'; }
}
