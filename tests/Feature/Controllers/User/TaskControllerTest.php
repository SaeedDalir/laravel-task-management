<?php

namespace Tests\Feature\Controllers\User;

use App\Enums\RoleEnum;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private function asUser(User $user): static
    {
        $token = Auth::guard('user')->fromUser($user);

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    #[Test]
    public function index_returns_only_owned_and_assigned_tasks_for_regular_user(): void
    {
        $owner = User::factory()->create(['role' => RoleEnum::USER->value]);
        $other = User::factory()->create(['role' => RoleEnum::USER->value]);

        $ownedTask = Task::factory()->create(['user_id' => $owner->id, 'title' => 'Owned']);
        $assignedTask = Task::factory()->create(['user_id' => $other->id, 'title' => 'Assigned']);
        $unrelatedTask = Task::factory()->create(['user_id' => $other->id, 'title' => 'Unrelated']);

        $assignedTask->users()->attach($owner->id);

        $response = $this->asUser($owner)->getJson(route('tasks.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data.data')
                ->where('data.meta.total', 2)
                ->etc()
        );

        $ids = collect($response->json('data.data'))->pluck('id')->all();
        $this->assertContains($ownedTask->id, $ids);
        $this->assertContains($assignedTask->id, $ids);
        $this->assertNotContains($unrelatedTask->id, $ids);
    }

    #[Test]
    public function index_applies_status_filter(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER->value]);

        $pendingTask = Task::factory()->create([
            'user_id' => $user->id,
            'status' => TaskStatusEnum::PENDING->value,
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'status' => TaskStatusEnum::COMPLETED->value,
        ]);

        $response = $this->asUser($user)->getJson(route('tasks.index', [
            'filter' => [
                'status' => TaskStatusEnum::PENDING->value,
            ],
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $ids = collect($response->json('data.data'))->pluck('id')->all();
        $this->assertSame([$pendingTask->id], $ids);
    }

    #[Test]
    public function show_returns_200_when_owner_views_task(): void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->asUser($owner)->getJson(route('tasks.show', $task));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('data.id', $task->id);
    }

    #[Test]
    public function store_returns_201_and_sets_owner_to_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER->value]);

        $response = $this->asUser($user)->postJson(route('tasks.store'), [
            'title' => 'My Task',
            'description' => 'Desc',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonPath('data.title', 'My Task');
        $taskId = $response->json('data.id');

        $this->assertDatabaseHas('tasks', [
            'id' => $taskId,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function update_returns_200_when_owner_updates_task(): void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id, 'title' => 'Old']);

        $response = $this->asUser($owner)->putJson(route('tasks.update', $task), [
            'title' => 'New',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('data.title', 'New');
        $task->refresh();
        $this->assertSame('New', $task->title);
    }

    #[Test]
    public function update_status_allows_owner_and_updates_completed_at(): void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $owner->id,
            'status' => TaskStatusEnum::PENDING->value,
            'completed_at' => null,
        ]);

        $response = $this->asUser($owner)->patchJson(route('tasks.updateStatus', $task), [
            'status' => TaskStatusEnum::COMPLETED->value,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $task->refresh();
        $this->assertSame(TaskStatusEnum::COMPLETED->value, $task->status->value);
        $this->assertNotNull($task->completed_at);
    }

    #[Test]
    public function destroy_allows_owner_to_delete_task(): void
    {
        $owner = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $this->asUser($owner)->deleteJson(route('tasks.destroy', $task))
            ->assertStatus(Response::HTTP_OK);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function destroy_allows_admin_to_delete_any_task(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN->value]);
        $task = Task::factory()->create();

        $this->asUser($admin)->deleteJson(route('tasks.destroy', $task))
            ->assertStatus(Response::HTTP_OK);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}

