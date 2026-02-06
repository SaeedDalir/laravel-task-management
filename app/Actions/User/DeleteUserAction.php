<?php

namespace App\Actions\User;

use App\Events\ActivityOccurred;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        $before = ['id' => $user->id, 'email' => $user->email];

        $user->delete();

        event(new ActivityOccurred(
            'user.deleted',
            "User deleted: {$user->email}",
            $user,
            $before,
            [],
            Auth::user()
        ));
    }
}
