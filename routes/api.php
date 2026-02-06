<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\TaskController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('users.register')->middleware('guest:user');
    Route::post('login', [AuthController::class, 'login'])->name('users.login')->middleware('guest:user');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('users.refresh')->middleware('auth:user');
});

Route::prefix('users')->middleware('auth:user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('can:viewAny,App\Models\User');
    Route::post('/', [UserController::class, 'store'])->name('users.store')->middleware('can:create,App\Models\User');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:view,user');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:update,user');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:delete,user');
});

Route::prefix('tasks')->middleware('auth:user')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index')->middleware('can:viewAny,App\Models\Task');
    Route::post('/', [TaskController::class, 'store'])->name('tasks.store')->middleware('can:create,App\Models\Task');
    Route::get('/{task}', [TaskController::class, 'show'])->name('tasks.show')->middleware('can:view,task');
    Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware('can:update,task');
    Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus')->middleware('can:update,task');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware('can:delete,task');
});
