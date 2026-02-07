<?php

namespace App\Http\Controllers\User;

use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Actions\Task\UpdateTaskStatusAction;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Requests\Task\TaskUpdateStatusRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TaskController extends BaseController
{
    public function __construct(private readonly CacheService $cacheService)
    {
        parent::__construct();
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $filters = (array) request('filter', []);
        $page = (int) request('page', 1);
        $size = (int) request('size', 15);

        $data = $this->cacheService->rememberTaskIndex($user, $page, $size, $filters, function () use ($user, $filters) {
            $tasks = Task::visibleFor($user)
                ->filter($filters)
                ->orderByDesc('id')
                ->paginateWithSize();

            return \format_pagination(TaskResource::collection($tasks));
        });

        return Response::success(
            message: '',
            data: $data
        );
    }

    public function show(Task $task)
    {
        return Response::success(message: '', data: TaskResource::make($task));
    }

    public function store(TaskStoreRequest $taskStoreRequest, CreateTaskAction $createTaskAction)
    {
        $task = $createTaskAction->execute($taskStoreRequest);

        return Response::success(message: '', data: TaskResource::make($task), code: 201);
    }

    public function update(TaskUpdateRequest $taskUpdateRequest, Task $task, UpdateTaskAction $updateTaskAction)
    {
        $task = $updateTaskAction->execute($taskUpdateRequest, $task);

        return Response::success(message: '', data: TaskResource::make($task));
    }

    public function updateStatus(TaskUpdateStatusRequest $taskUpdateStatusRequest, Task $task, UpdateTaskStatusAction $updateTaskStatusAction)
    {
        $task = $updateTaskStatusAction->execute($taskUpdateStatusRequest, $task);

        return Response::success(message: '', data: TaskResource::make($task));
    }

    public function destroy(Task $task, DeleteTaskAction $deleteTaskAction)
    {
        $deleteTaskAction->execute($task);

        return Response::success(message: '', data: null);
    }
}
