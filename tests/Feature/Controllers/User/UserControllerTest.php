<?php

namespace Tests\Feature\Controllers\User;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private function asUser(User $user): static
    {
        $token = Auth::guard('user')->fromUser($user);

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    #[Test]
    public function index_returns_200_with_data_when_admin(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN->value]);
        User::factory()->count(2)->create();

        $response = $this->asUser($admin)->getJson(route('users.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data.data')
                ->has('data.meta')
                ->where('data.meta.total', 3)
                ->etc()
        );
    }

    #[Test]
    public function show_returns_200_when_user_views_self(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER->value]);

        $response = $this->asUser($user)->getJson(route('users.show', $user));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.id', $user->id)
                ->where('data.email', $user->email)
                ->etc()
        );
    }

    #[Test]
    public function store_returns_201_and_creates_user_when_admin(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN->value]);

        $response = $this->asUser($admin)->postJson(route('users.store'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data')
                ->where('data.email', 'new@example.com')
                ->where('data.name', 'New User')
                ->where('data.role', RoleEnum::USER->value)
                ->etc()
        );
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    #[Test]
    public function update_returns_200_when_user_updates_self(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::USER->value, 'name' => 'Old Name']);

        $response = $this->asUser($user)->putJson(route('users.update', $user), [
            'name' => 'New Name',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('data.name', 'New Name');
        $user->refresh();
        $this->assertSame('New Name', $user->name);
    }

    #[Test]
    public function destroy_returns_200_and_soft_deletes_when_admin(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::ADMIN->value]);
        $target = User::factory()->create();

        $response = $this->asUser($admin)->deleteJson(route('users.destroy', $target));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }
}
