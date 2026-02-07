<?php

namespace App\Services;

use App\Models\User;
use App\Services\Cache\CacheStrategy;
use Closure;

class CacheService
{
    private const TTL_DAY = 86400;

    public function __construct(private readonly CacheStrategy $strategy) {}

    public function rememberTaskIndex(User $user, int $page, int $size, array $filters, Closure $callback): array
    {
        $cacheKey = "tasks:index:user:{$user->id}:page:{$page}:size:{$size}:filter:".md5(json_encode($filters));

        return $this->strategy->remember($cacheKey, ['tasks', "tasks:user:{$user->id}"], self::TTL_DAY, $callback);
    }

    public function rememberUserIndex(int $page, int $size, Closure $callback): array
    {
        $cacheKey = "users:index:page:{$page}:size:{$size}";

        return $this->strategy->remember($cacheKey, ['users'], self::TTL_DAY, $callback);
    }

    public function rememberUserShow(User $user, Closure $callback): array
    {
        $cacheKey = "users:show:{$user->id}";

        return $this->strategy->remember($cacheKey, ['users', "users:show:{$user->id}"], self::TTL_DAY, $callback);
    }

    public function invalidateTaskCaches(): void
    {
        $this->strategy->invalidate(['tasks']);
    }

    public function invalidateUserCaches(): void
    {
        $this->strategy->invalidate(['users']);
    }
}
