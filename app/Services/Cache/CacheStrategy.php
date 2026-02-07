<?php

namespace App\Services\Cache;

use Closure;

interface CacheStrategy
{
    public function remember(string $key, array $tags, int $ttl, Closure $callback): mixed;

    public function invalidate(array $tags): void;
}
