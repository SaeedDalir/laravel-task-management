<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class RefreshTokenAction
{
    public function __construct(
        private readonly RespondWithTokenAction $respondWithTokenAction,
    ) {}

    public function execute(): array
    {
        $refreshedToken = Auth::refresh();
        auth()->setToken($refreshedToken);

        return $this->respondWithTokenAction->execute($refreshedToken);
    }
}
