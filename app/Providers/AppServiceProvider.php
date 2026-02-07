<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\User;
use App\Services\Cache\CacheStrategy;
use App\Services\Cache\SimpleCacheStrategy;
use App\Services\Cache\TaggedCacheStrategy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');

        $this->app->bind(CacheStrategy::class, function () {
            $driver = config('cache.default');

            return match ($driver) {
                'redis', 'memcached', 'array' => new TaggedCacheStrategy,
                default => new SimpleCacheStrategy,
            };
        });
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'users' => User::class,
            'tasks' => Task::class,
        ]);
    }
}
