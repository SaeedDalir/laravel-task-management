<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status?->value,
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}

