<?php

namespace App\Observers;

use App\Models\News;
use App\Support\Notify;

class NewsObserver
{
    public function created(News $news): void
    {
        Notify::newsCreated($news);
    }
}
