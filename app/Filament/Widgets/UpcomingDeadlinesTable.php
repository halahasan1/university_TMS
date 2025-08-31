<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UpcomingDeadlinesTable extends BaseTableWidget
{
    protected static ?string $heading = 'Upcoming Deadlines (7d)';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getTableQuery(): Builder
    {
        return Task::query()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->orderBy('due_date');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->label('Task')->limit(40)->searchable(),
            Tables\Columns\TextColumn::make('assignedTo.name')->label('Assignee')->toggleable(),
            Tables\Columns\TextColumn::make('due_date')->dateTime('M d, H:i'),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'info'    => 'in_progress',
                    'success' => 'completed',
                ])
                ->formatStateUsing(fn($state)=>Str::headline(str_replace('_',' ',$state))),
        ];
    }
}
