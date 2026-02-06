<?php

namespace App\Actions\Task;

use App\Enums\TaskStatusEnum;
use App\Events\ActivityOccurred;
use App\Http\Requests\Task\TaskUpdateStatusRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class UpdateTaskStatusAction
{
    public function execute(TaskUpdateStatusRequest $request, Task $task): Task
    {
        $before = [
            'status' => $task->status?->value,
            'completed_at' => $task->completed_at,
        ];

        $status = $request->validated('status');

        if ($status === TaskStatusEnum::COMPLETED->value) {
            $task->completed_at = now();
        }

        if ($status === TaskStatusEnum::PENDING->value) {
            $task->completed_at = null;
        }

        $task->status = $status;
        $task->save();

        $after = [
            'status' => $task->status?->value,
            'completed_at' => $task->completed_at,
        ];

        event(new ActivityOccurred(
            'task.status_updated',
            "Task status updated: {$task->id}",
            $task,
            $before,
            $after,
            Auth::user()
        ));

        return $task->fresh();
    }
}

