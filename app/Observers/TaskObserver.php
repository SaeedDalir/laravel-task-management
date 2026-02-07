<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\CacheService;

class TaskObserver
{
    public function __construct(private readonly CacheService $cacheService) {}

    public function created(Task $task): void
    {
        $this->cacheService->invalidateTaskCaches();
    }

    public function updated(Task $task): void
    {
        $this->cacheService->invalidateTaskCaches();
    }

    public function deleted(Task $task): void
    {
        $this->cacheService->invalidateTaskCaches();
    }
}
