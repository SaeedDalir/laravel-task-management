<?php

namespace App\Actions\Auth;

use App\Enums\RoleEnum;
use App\Events\ActivityOccurred;
use App\Http\Requests\User\UserRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterUserAction
{
    public function __construct(
        private readonly RespondWithTokenAction $respondWithTokenAction,
    ) {}

    public function execute(UserRegisterRequest $request): array
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => RoleEnum::USER->value,
        ]);

        event(new ActivityOccurred('user.registered', "User registered: {$user->email}", $user, [], [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ], $user));

        $token = Auth::guard('user')->fromUser($user);
        auth()->setToken($token);

        return $this->respondWithTokenAction->execute($token);
    }
}
