<?php

namespace App\Services\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class TaggedCacheStrategy implements CacheStrategy
{
    public function remember(string $key, array $tags, int $ttl, Closure $callback): mixed
    {
        return Cache::tags($tags)->remember($key, $ttl, $callback);
    }

    public function invalidate(array $tags): void
    {
        Cache::tags($tags)->flush();
    }
}
