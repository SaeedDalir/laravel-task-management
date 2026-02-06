<?php

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_user_register_with_valid_data(): void
    {
        $response = $this->postJson(route('users.register'), [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data.access_token')
                ->has('data.user')
                ->where('data.user.email', 'new@example.com')
                ->where('data.user.name', 'Test User')
                ->etc()
        );
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }


    #[Test]
    public function can_user_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson(route('users.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data.access_token')
                ->has('data.user')
                ->where('data.user.email', $user->email)
                ->where('data.user.id', $user->id)
                ->etc()
        );
    }

    #[Test]
    public function can_user_refresh_token(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $loginResponse = $this->postJson(route('users.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(Response::HTTP_OK);
        $token = $loginResponse->json('data.access_token');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('users.refresh'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn (AssertableJson $json) => $json->has('data.access_token')
                ->has('data.user')
                ->where('data.user.email', $user->email)
                ->etc()
        );
    }
}
