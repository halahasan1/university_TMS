<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OverdueTasksTable extends BaseTableWidget
{
    protected static ?string $heading = 'Overdue Tasks';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getTableQuery(): Builder
    {
        return Task::query()
            ->where('status','!=','completed')
            ->whereNotNull('due_date')
            ->where('due_date','<', now())
            ->orderBy('due_date');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->limit(40),
            Tables\Columns\TextColumn::make('assignedTo.name')->label('Assignee'),
            Tables\Columns\TextColumn::make('due_date')->dateTime('M d, H:i'),
        ];
    }
}
