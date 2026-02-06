<?php

namespace App\Actions\User;

use App\Enums\RoleEnum;
use App\Events\ActivityOccurred;
use App\Http\Requests\User\UserStoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreateUserAction
{
    public function execute(UserStoreRequest $request): User
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role') ?? RoleEnum::USER->value,
        ]);

        event(new ActivityOccurred(
            'user.created',
            "User created: {$user->email}",
            $user,
            [],
            ['id' => $user->id, 'email' => $user->email, 'name' => $user->name, 'role' => $user->role?->value],
            Auth::user()
        ));

        return $user;
    }
}
