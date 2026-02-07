<?php

namespace App\Actions\Task;

use App\Events\ActivityOccurred;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class UpdateTaskAction
{
    public function execute(TaskUpdateRequest $request, Task $task): Task
    {
        $before = [
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'assigned_user_ids' => $task->users()->pluck('users.id')->all(),
        ];

        $data = $request->validated();

        $assignedUserIds = $data['assigned_user_ids'] ?? null;
        unset($data['assigned_user_ids']);

        $task->fill($data)->save();

        if ($assignedUserIds !== null) {
            $task->users()->sync($assignedUserIds);
            $task->touch();
        }

        $after = [
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'assigned_user_ids' => $task->users()->pluck('users.id')->all(),
        ];

        event(new ActivityOccurred(
            'task.updated',
            "Task updated: {$task->id}",
            $task,
            $before,
            $after,
            Auth::user()
        ));

        return $task->fresh();
    }
}

