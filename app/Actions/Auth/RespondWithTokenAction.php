<?php

namespace App\Actions\Auth;

use App\Http\Resources\User\UserResource;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class RespondWithTokenAction
{
    public function execute(string $token = ''): array
    {
        $user = $this->guard()->user();

        return [
            'access_token' => $token,
            'user' => UserResource::make($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->guard()->factory()->getTTL() * 60,
        ];
    }

    private function guard(): Guard|StatefulGuard
    {
        return Auth::guard();
    }
}
