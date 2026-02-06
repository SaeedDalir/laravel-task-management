<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', Rule::in(TaskStatusEnum::values())],
            'assigned_user_ids' => ['sometimes', 'array'],
            'assigned_user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}

