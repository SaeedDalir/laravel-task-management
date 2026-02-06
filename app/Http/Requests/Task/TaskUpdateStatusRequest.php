<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('task')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(TaskStatusEnum::values())],
        ];
    }
}

