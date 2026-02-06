<?php

namespace App\Actions\User;

use App\Events\ActivityOccurred;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateUserAction
{
    public function execute(UserUpdateRequest $request, User $user): User
    {
        $before = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->value,
        ];

        $data = $request->validated();
        if (array_key_exists('password', $data) && $data['password'] !== null && $data['password'] !== '') {
            $user->password = $data['password'];
        }
        unset($data['password'], $data['password_confirmation']);
        $user->fill($data)->save();

        event(new ActivityOccurred(
            'user.updated',
            "User updated: {$user->email}",
            $user,
            $before,
            ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role?->value],
            Auth::user()
        ));

        return $user->fresh();
    }
}
