<?php

namespace App\Http\Controllers\User;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Response;

class UserController extends BaseController
{
    public function __construct(private readonly CacheService $cacheService)
    {
        parent::__construct();
    }

    public function index()
    {
        $page = (int) request('page', 1);
        $size = (int) request('size', 15);

        $data = $this->cacheService->rememberUserIndex($page, $size, function () {
            $users = User::orderBy('id')->paginateWithSize();

            return \format_pagination(UserResource::collection($users));
        });

        return Response::success(
            message: '',
            data: $data
        );
    }

    public function show(User $user)
    {
        $data = $this->cacheService->rememberUserShow($user, function () use ($user) {
            return UserResource::make($user)->resolve();
        });

        return Response::success(message: '', data: $data);
    }

    public function store(UserStoreRequest $userStoreRequest, CreateUserAction $createUserAction)
    {
        $user = $createUserAction->execute($userStoreRequest);

        return Response::success(message: '', data: UserResource::make($user), code: 201);
    }

    public function update(UserUpdateRequest $userUpdateRequest, User $user, UpdateUserAction $updateUserAction)
    {
        $user = $updateUserAction->execute($userUpdateRequest, $user);

        return Response::success(message: '', data: UserResource::make($user));
    }

    public function destroy(User $user, DeleteUserAction $deleteUserAction)
    {
        $deleteUserAction->execute($user);

        return Response::success(message: '', data: null);
    }
}
