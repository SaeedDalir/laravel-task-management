<?php

namespace App\Actions\Task;

use App\Enums\TaskStatusEnum;
use App\Events\ActivityOccurred;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class CreateTaskAction
{
    public function execute(TaskStoreRequest $request): Task
    {
        $user = Auth::user();

        $task = Task::create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'status' => $request->validated('status') ?? TaskStatusEnum::PENDING->value,
            'due_date' => $request->validated('due_date'),
            'user_id' => $user->id,
        ]);

        $assignedUserIds = $request->validated('assigned_user_ids') ?? [];
        if ($assignedUserIds !== []) {
            $task->users()->sync($assignedUserIds);
            $task->touch();
        }

        event(new ActivityOccurred(
            'task.created',
            "Task created: {$task->id}",
            $task,
            [],
            [
                'id' => $task->id,
                'title' => $task->title,
                'user_id' => $task->user_id,
                'status' => $task->status?->value,
            ],
            $user
        ));

        return $task;
    }
}

