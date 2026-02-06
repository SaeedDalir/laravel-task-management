<?php

namespace App\Listeners;

use App\Actions\ActivityLog\ActivityLogAction;
use App\Events\ActivityOccurred;

class ActivityLogListener
{
    public function __construct(
        private readonly ActivityLogAction $activityLogAction,
    ) {}

    public function handle(ActivityOccurred $event): void
    {
        $this->activityLogAction->execute($event);
    }
}
