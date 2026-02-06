<?php

namespace App\Actions\Auth;

use App\Events\ActivityOccurred;
use App\Exceptions\MessageException;
use App\Http\Requests\User\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginUserAction
{
    public function __construct(
        private readonly RespondWithTokenAction $respondWithTokenAction,
    ) {}

    public function execute(UserLoginRequest $userLoginRequest)
    {
        $user = $userLoginRequest->getUserByEmail();

        if (! $user) {
            $this->throwInvalidCredentials();
        }

        $token = Auth::attempt([
            'email' => $user->email,
            'password' => $userLoginRequest->password,
        ]);

        if (! $token) {
            $this->throwInvalidCredentials();
        }

        event(new ActivityOccurred('user.logged_in', "User logged in: {$user->email}", $user, [], [], $user));

        return $this->respondWithTokenAction->execute($token);
    }

    private function throwInvalidCredentials(): void
    {
        throw new MessageException(
            'Invalid credentials.',
            Response::HTTP_UNAUTHORIZED
        );
    }
}
