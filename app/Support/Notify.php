<?php

namespace App\Support;

use App\Models\News;
use App\Models\Task;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class Notify
{
    private static function recordUrl(string $resourceClass, $record): string
    {
        try { return $resourceClass::getUrl('view', ['record' => $record]); } catch (\Throwable $e) {}
        try { return $resourceClass::getUrl('edit', ['record' => $record]); } catch (\Throwable $e) {}
        return $resourceClass::getUrl();
    }

    public static function taskAssigned(Task $task): void
    {
        if (! $task->assignedTo) return;

        $url = self::recordUrl(\App\Filament\Resources\TaskResource::class, $task);

        Notification::make()
            ->title('New Task Assigned')
            ->body($task->title)
            ->icon('heroicon-o-clipboard-document-check')
            ->actions([
                Action::make('Open')->url($url, true)->button(),
            ])
            ->sendToDatabase($task->assignedTo);
    }

    public static function taskReassigned(Task $task, ?User $oldUser, ?User $newUser): void
    {
        if (! $newUser) return;

        $url = self::recordUrl(\App\Filament\Resources\TaskResource::class, $task);

        Notification::make()
            ->title('Task Assigned to You')
            ->body($task->title)
            ->icon('heroicon-o-arrow-right-circle')
            ->actions([
                Action::make('Open')->url($url, true)->button(),
            ])
            ->sendToDatabase($newUser);
    }

    public static function newsCreated(News $news): void
    {
        $admins = User::role('super_admin')->get();
        $url = self::recordUrl(\App\Filament\Resources\NewsResource::class, $news);

        Notification::make()
            ->title('New News Posted')
            ->body($news->title)
            ->icon('heroicon-o-newspaper')
            ->actions([
                Action::make('Open')->url($url, true)->button(),
            ])
            ->sendToDatabase($admins);
    }

    public static function liked(string $what, string $byName, User $owner, string $url): void
    {
        Notification::make()
            ->title("New like on your {$what}")
            ->body("{$byName} liked your {$what}.")
            ->icon('heroicon-o-hand-thumb-up')
            ->actions([
                Action::make('Open')->url($url, true)->button(),
            ])
            ->sendToDatabase($owner);
    }
}
