<?php

namespace App\Actions\Task;

use App\Events\ActivityOccurred;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DeleteTaskAction
{
    public function execute(Task $task): void
    {
        $before = [
            'id' => $task->id,
            'title' => $task->title,
            'user_id' => $task->user_id,
        ];

        $task->delete();

        event(new ActivityOccurred(
            'task.deleted',
            "Task deleted: {$task->id}",
            $task,
            $before,
            [],
            Auth::user()
        ));
    }
}

