<?php

namespace App\Services\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class SimpleCacheStrategy implements CacheStrategy
{
    private const REGISTRY_PREFIX = 'cache_registry:';

    public function remember(string $key, array $tags, int $ttl, Closure $callback): mixed
    {
        foreach ($tags as $tag) {
            $this->registerKey($tag, $key);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    public function invalidate(array $tags): void
    {
        foreach ($tags as $tag) {
            $registryKey = self::REGISTRY_PREFIX.$tag;
            $keys = Cache::get($registryKey, []);

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Cache::forget($registryKey);
        }
    }

    private function registerKey(string $tag, string $key): void
    {
        $registryKey = self::REGISTRY_PREFIX.$tag;
        $keys = Cache::get($registryKey, []);

        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            Cache::forever($registryKey, $keys);
        }
    }
}
