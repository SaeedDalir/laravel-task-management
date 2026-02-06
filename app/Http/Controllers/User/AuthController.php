<?php

namespace App\Http\Controllers\User;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RefreshTokenAction;
use App\Actions\Auth\RegisterUserAction;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use Illuminate\Support\Facades\Response;

class AuthController extends BaseController
{
    public function register(
        UserRegisterRequest $userRegisterRequest,
        RegisterUserAction $registerUserAction,
    ) {
        $response = $registerUserAction->execute($userRegisterRequest);

        return Response::success(
            message: '',
            data: $response,
        );
    }

    public function login(
        UserLoginRequest $userLoginRequest,
        LoginUserAction $loginUserAction,
    ) {
        $response = $loginUserAction->execute($userLoginRequest);

        return Response::success(
            message: '',
            data: $response,
        );
    }

    public function refresh(RefreshTokenAction $refreshTokenAction)
    {
        $response = $refreshTokenAction->execute();

        return Response::success(
            message: '',
            data: $response,
        );
    }
}
