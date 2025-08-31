<?php

namespace App\Providers;

use App\Models\Like;
use App\Models\News;
use App\Models\Task;
use App\Observers\LikeObserver;
use App\Observers\NewsObserver;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        News::observe(NewsObserver::class);
        Like::observe(LikeObserver::class);
    }
    protected $policies = [
        Task::class => \App\Policies\TaskPolicy::class,
    ];

}
