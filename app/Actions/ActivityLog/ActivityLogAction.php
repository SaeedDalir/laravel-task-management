<?php

namespace App\Actions\ActivityLog;

use App\Events\ActivityOccurred;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class ActivityLogAction
{
    public function execute(ActivityOccurred $event): ActivityLog
    {
        $request = request();
        $user = $event->user;

        $log = ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $event->action,
            'description' => $event->description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'subject_type' => $event->subject?->getMorphClass(),
            'subject_id' => $event->subject?->getKey(),
            'before_value' => $event->before ?: null,
            'after_value' => $event->after ?: null,
        ]);

        Log::info("Activity: {$event->action}", [
            'user_id' => $user?->id,
            'subject' => $event->subject ? get_class($event->subject).'#'.$event->subject->getKey() : null,
        ]);

        return $log;
    }
}
