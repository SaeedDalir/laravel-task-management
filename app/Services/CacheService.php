<?php

namespace App\Services;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const TTL_DAY = 86400;

    public function rememberTaskIndex(User $user, int $page, int $size, array $filters, Closure $callback): array
    {
        $cacheKey = "tasks:index:user:{$user->id}:page:{$page}:size:{$size}:filter:".md5(json_encode($filters));

        return Cache::tags(['tasks', "tasks:user:{$user->id}"])->remember($cacheKey, self::TTL_DAY, $callback);
    }

    public function rememberUserIndex(int $page, int $size, Closure $callback): array
    {
        $cacheKey = "users:index:page:{$page}:size:{$size}";

        return Cache::tags(['users'])->remember($cacheKey, self::TTL_DAY, $callback);
    }

    public function rememberUserShow(User $user, Closure $callback): array
    {
        $cacheKey = "users:show:{$user->id}";

        return Cache::tags(['users', "users:show:{$user->id}"])->remember($cacheKey, self::TTL_DAY, $callback);
    }

    public function invalidateTaskCaches(): void
    {
        Cache::tags(['tasks'])->flush();
    }

    public function invalidateUserCaches(): void
    {
        Cache::tags(['users'])->flush();
    }
}
