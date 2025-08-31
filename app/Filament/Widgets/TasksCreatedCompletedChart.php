<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksCreatedCompletedChart extends ChartWidget
{
    protected static ?string $heading = 'Created vs Completed (30d)';
    protected int|string|array $columnSpan = 'full';


    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getData(): array
    {
        $from = now()->copy()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        $created = Task::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) d, COUNT(*) c')->groupBy('d')->pluck('c','d');

        $completed = Task::where('status','completed')
            ->whereBetween('updated_at', [$from, $to])
            ->selectRaw('DATE(updated_at) d, COUNT(*) c')->groupBy('d')->pluck('c','d');

        $labels=[];$dataC=[];$dataDone=[];
        $cursor=$from->copy();
        while($cursor->lte($to)){
            $key=$cursor->toDateString();
            $labels[]=$cursor->format('M d');
            $dataC[]=(int)($created[$key]??0);
            $dataDone[]=(int)($completed[$key]??0);
            $cursor->addDay();
        }

        return [
            'datasets' => [
                ['label'=>'Created','data'=>$dataC],
                ['label'=>'Completed','data'=>$dataDone],
            ],
            'labels'=>$labels,
        ];
    }

    protected function getType(): string { return 'line'; }
}
