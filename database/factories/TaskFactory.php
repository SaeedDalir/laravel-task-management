<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'status' => TaskStatusEnum::PENDING->value,
            'due_date' => null,
            'completed_at' => null,
            'user_id' => User::factory(),
        ];
    }
}

