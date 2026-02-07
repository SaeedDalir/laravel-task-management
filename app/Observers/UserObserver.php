<?php

namespace App\Observers;

use App\Models\User;
use App\Services\CacheService;

class UserObserver
{
    public function __construct(private readonly CacheService $cacheService) {}

    public function created(User $user): void
    {
        $this->cacheService->invalidateUserCaches();
    }

    public function updated(User $user): void
    {
        $this->cacheService->invalidateUserCaches();
    }

    public function deleted(User $user): void
    {
        $this->cacheService->invalidateUserCaches();
    }
}
