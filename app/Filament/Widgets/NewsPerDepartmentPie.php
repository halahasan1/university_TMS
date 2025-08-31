<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Widgets\ChartWidget;

class NewsPerDepartmentPie extends ChartWidget
{
    protected static ?string $heading = 'News per Department';
    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getData(): array
    {
        $rows = News::query()
            ->join('users','users.id','=','news.user_id')
            ->join('profiles','profiles.user_id','=','users.id')
            ->join('departments','departments.id','=','profiles.department_id')
            ->selectRaw('departments.name dept, COUNT(news.id) total')
            ->groupBy('departments.name')
            ->orderBy('total','desc')
            ->pluck('total','dept');

        return [
            'datasets' => [['label' => 'News', 'data' => $rows->values()->all()]],
            'labels'   => $rows->keys()->values()->all(),
        ];
    }

    protected function getType(): string { return 'pie'; }
}
