<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'users' => User::class,
            'tasks' => Task::class,
        ]);
    }
}
