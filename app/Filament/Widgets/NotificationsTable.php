<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NotificationsTable extends BaseTableWidget
{
    protected static ?string $heading = 'Notifications';
    public static function canView(): bool { return auth()->check(); }
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';


    protected function getTableQuery(): Builder
    {
        return auth()->user()
            ->notifications()
            ->getQuery()
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('data.title')
                ->label('Title')
                ->formatStateUsing(fn (DatabaseNotification $r) => Arr::get($r->data, 'title', 'Notification'))
                ->limit(50)
                ->wrap(),
            Tables\Columns\TextColumn::make('data.body')
                ->label('Body')
                ->formatStateUsing(fn (DatabaseNotification $r) => Str::limit((string) Arr::get($r->data, 'body', ''), 80)),
            Tables\Columns\TextColumn::make('created_at')
                ->since()
                ->label('When'),
            Tables\Columns\BadgeColumn::make('read_at')
                ->label('Status')
                ->colors([
                    'primary' => fn ($state) => is_null($state), // Unread
                    'gray'    => fn ($state) => ! is_null($state),
                ])
                ->formatStateUsing(fn ($state) => is_null($state) ? 'Unread' : 'Read'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('open')
                ->label('Open')
                ->url(function (DatabaseNotification $r) {
                    $actions = Arr::get($r->data, 'actions', []);
                    $first   = is_array($actions) ? ($actions[0] ?? null) : null;
                    return is_array($first) ? ($first['url'] ?? null) : null;
                }, true)
                ->hidden(fn (DatabaseNotification $r) => blank(Arr::get($r->data, 'actions.0.url'))),
            Tables\Actions\Action::make('mark_read')
                ->label('Mark read')
                ->action(fn (DatabaseNotification $r) => $r->markAsRead())
                ->hidden(fn (DatabaseNotification $r) => ! is_null($r->read_at)),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('mark_all')
                ->label('Mark all as read')
                ->action(function () {
                    auth()->user()->unreadNotifications->markAsRead();
                })
                ->color('gray'),
        ];
    }
}
