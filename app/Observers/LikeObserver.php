<?php

namespace App\Observers;

use App\Models\Like;
use App\Models\News;
use App\Models\Task;
use App\Models\Comment;
use App\Support\Notify;

class LikeObserver
{
    private function resourceUrl(string $resource, $record): string
    {
        try { return $resource::getUrl('view', ['record' => $record]); } catch (\Throwable $e) {}
        try { return $resource::getUrl('edit', ['record' => $record]); } catch (\Throwable $e) {}
        return $resource::getUrl();
    }

    public function created(Like $like): void
    {
        $by = $like->user;
        if (! $by) return;

        $owner = null;
        $url   = url('/');
        $what  = 'item';

        $likeable = $like->likeable;

        if ($likeable instanceof News) {
            $owner = $likeable->user;
            $url   = $this->resourceUrl(\App\Filament\Resources\NewsResource::class, $likeable);
            $what  = 'news';
        }

        if (class_exists(Comment::class) && $likeable instanceof Comment) {
            $owner = $likeable->user;
            $parent = $likeable->commentable;

            if ($parent instanceof News) {
                $url  = $this->resourceUrl(\App\Filament\Resources\NewsResource::class, $parent);
                $what = 'comment on your news';
            } elseif ($parent instanceof Task) {
                $url  = $this->resourceUrl(\App\Filament\Resources\TaskResource::class, $parent);
                $what = 'comment on your task';
            }
        }

        if ($owner && $owner->isNot($by)) {
            Notify::liked($what, $by->name, $owner, $url);
        }
    }
}
