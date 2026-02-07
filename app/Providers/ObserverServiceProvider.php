<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use App\Observers\TaskObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        User::observe(UserObserver::class);
    }
}
